<?php

namespace JHV\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JHVUserExtension extends Extension
{
    
    protected $emails;
    
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        // Definição de parâmetros
        $container->setParameter('jhv_user.parameter.translation_domain', $config['default_translation_domain']);
        $container->setParameter('jhv_user.canonicalizer.class', $config['classes']['canonicalizer']);
        $container->setParameter('jhv_user.manager.helper.class', $config['classes']['user_helper']);
        $container->setParameter('jhv_user.manager.handler.class', $config['classes']['manager_handler']);
        $container->setParameter('jhv_user.manager.router.class', $config['classes']['router']);
        $container->setParameter('jhv_user.mailer.class', $config['classes']['mailer']);
        $container->setParameter('jhv_user.template.manager.class', $config['classes']['template_manager']);
        $container->setParameter('jhv_user.template.renderer.class', $config['classes']['template_renderer']);
        
        // Verificação de em´s para os roteadores
        if (array_diff(array_keys($config['managers']), array_keys($config['routes']))) {
            throw new \RunTimeException('You must register call manager on both locale, routes and managers.');
        }
        
        // Processamento da geração de serviços para gerenciamento de usuários
        $this->processManagers($container, $config['managers'], $config['classes']);
        $this->processTemplates($container, $config['managers'], $config['classes']);
        
        // Registrar roteadores (e verificar grupos)
        foreach ($config['routes'] as $manager => $data) {
            if (false === $data['groups']['enabled']) {
                unset($config['routes'][$manager]['groups']);
            }
            
            unset($config['routes'][$manager]['groups']['enabled']);
        }
        $container->setParameter('jhv_user.parameter.registration.routes', $config['routes']);
        
        // Definir parâmetros de resetting
        $this->processResettingParameters($container, $config['managers'], $config['email']);
        
        // E-mails
        $container->setParameter('jhv_user.parameter.emails', $this->emails);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services/services.yml');
        $loader->load('services/security.yml');
        $loader->load('services/mailer.yml');
        $loader->load('services/form.yml');
        $loader->load('services/twig.yml');
        $loader->load('services/listeners.yml');
    }
    
    /**
     * Processar gerenciadores de usuários.
     * 
     * O método irá verificar quais os gerenciadores configurados para
     * que possa haver mais de um EntityManager para usuários, fazendo
     * assim com que possa haver diferentes usuários baseados a mesma
     * referência de gerenciamento.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array     $managers
     * @param array     $classes
     */
    protected function processManagers(ContainerBuilder $container, array $managers, array $classes)
    {
        foreach ($managers as $key => $manager) {
            $user_manager_class     = (null === $manager['user_manager']) ? $classes['user_manager'] : $manager['user_manager'];
            $group_manager_class    = (null === $manager['group_manager']) ? $classes['group_manager'] : $manager['group_manager'];
            $userDefinitionId       = sprintf('jhv_user.manager.%s.user', strtolower($key));
            $groupDefinitionId      = sprintf('jhv_user.manager.%s.group', strtolower($key));
            
            ### Definição de um serviço para gerenciamento de usuários (de acordo com managers da configuração)
            $container
                ->setDefinition($userDefinitionId, new Definition(
                    $user_manager_class,
                    array(
                        new Reference('jhv_user.manager.user_helper'),
                        $manager['firewall_name'],
                        new Reference(sprintf('doctrine.orm.%s_entity_manager', $manager['connection'])),
                        $manager["user_class"],
                    )
                ))
                // Definindo serviço como não público e com identificador para processar no compilador
                ->addTag('jhv_user.user_manager', array('identifier' => $key))
                ->setPublic(false)
            ;
            
            ### Definição de um serviço para gerenciamento de grupos (de acordo com managers da configuração)
            if (null !== $manager["group_class"] && class_exists($manager["group_class"])) {
                $container
                    ->setDefinition($groupDefinitionId, new Definition(
                        $group_manager_class,
                        array(
                            new Reference(sprintf('doctrine.orm.%s_entity_manager', $manager['connection'])),
                            $manager["group_class"],
                        )
                    ))
                    // Definindo serviço como não público e com identificador para processar no compilador
                    ->addTag('jhv_user.group_manager', array('identifier' => $key))
                    ->setPublic(false)
                ;
            }
            
            // Criar provedores para autenticação
            $this->createUserAuthenticationProvider($container, $key, $userDefinitionId, $classes);
        }
    }
    
    protected function processTemplates(ContainerBuilder $container, array $managers)
    {
        $templateFiles = array();
        foreach ($managers as $key => $data) {
            $templates = $data['templates'];
            $templateFiles[$key] = $templates;
            
            // Efetuar criação do serviço de template renderer
            $container
                ->setDefinition(sprintf('jhv_user.template.%s_renderer', $key), new Definition(
                    $container->getParameter('jhv_user.template.renderer.class'), array(
                        new Reference('twig'),
                        new Definition($container->getParameter('jhv_user.template.manager.class'), array(
                            $templates['default_layout'],
                            $templates['content_block'],
                            $templates['files'],
                        ))
                    )
                ))
            ;
        }
        
        $container->setParameter('jhv_user.parameter.templates', $templateFiles);
    }
    
    /**
     * Efetuar a criação de provedores para cada ObjectManager da posição.
     * O provedor servirá para definição no firewall.
     * 
     * Servirá como um atalho para localização do usuário, podendo ser de
     * localização por username ou e-mail.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string    $ident          Identificador do ObjectManager && Provedor
     * @param string    $definitionId   Identificador do serviço ObjectManager
     * @param array     $classes        Classes configuradas
     */
    protected function createUserAuthenticationProvider(ContainerBuilder $container, $ident, $definitionId, array $classes)
    {
        // Definição de provider para busca de usuário por nome com basamento no EntityManager
        $container->setDefinition('jhv_user.' . strtolower($ident) . '_provider.username', new Definition(
            $classes['providers']['auth_by_username'], array(
                new Reference($definitionId),
            )
        ))
        ->setPublic(false);

        // Definição de provider para busca de usuário por e-mail ou nome de usuário baseado no EntityManager
        $container->setDefinition('jhv_user.' . strtolower($ident) . '_provider.username_or_email', new Definition(
            $classes['providers']['auth_by_email'], array(
                new Reference($definitionId),
            )
        ))
        ->setPublic(false);
    }
    
    protected function processResettingParameters(ContainerBuilder $container, array $managers, $emailConfig)
    {
        $tokensTtl  = array();
        
        foreach ($managers as $key => $manager) {
            $container->setParameter(sprintf('jhv_user.parameter.resetting.%s.token_ttl', $key), $manager['resetting']['token_time_to_live']);
            $tokensTtl[$key] = $manager['resetting']['token_time_to_live'];
            $resetting = $manager['resetting'];
            
            // Configuração de informações de resetting
            $this->emails[$key]['resetting'] = array(
                'from_address'  => (null !== $resetting['email']['from_address']) ? $resetting['email']['from_address'] : $emailConfig['from_address'],
                'from_sender'   => (null !== $resetting['email']['from_sender']) ? $resetting['email']['from_sender'] : $emailConfig['from_sender']
            );
        }
        
        // Definir parâmetros na configuração
        $container->setParameter('jhv_user.parameter.tokens_ttl', $tokensTtl);
    }
    
}
