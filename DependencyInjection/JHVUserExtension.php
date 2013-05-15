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
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        // Inicializacao de variaveis
        $routes     = array();
        $emails     = array();
        $tokens     = array();
        $templates  = array();
        
        // Definicao de parametros
        $this->createContainerParameters($container, $config);
        
        // Processar as acoes referentes aos gerenciadores
        foreach ($config['managers'] as $key => $data) {
            // Criar o gerenciador
            $this->createManagerDependencies($container, $key, $data);
            
            // Criar provedores de autenticacao
            $this->createUserAuthenticationProvider($container, $key);
            
            // Templates
            $this->createTemplateDefinition($container, $key, $data);
            
            // Registro dos templates
            $templates[$key] = $this->getTemplates($data);
            
            // Criação de formFactories
            $this->createFormFactoryDefinition($container, $key, $data);
            
            // Verificar se o roteamento de esta habilitado
            if (true === $config['enabled_routing']) {
                $routes[$key] = $this->getRoutes($data);
            }
            
            // E-mails
            $emails[$key]['resetting']['from_sender'] = (null !== $data['resetting']['email']['from_sender']) ?: $config['email']['from_sender'];
            $emails[$key]['resetting']['from_address'] = (null !== $data['resetting']['email']['from_address']) ?: $config['email']['from_address'];
            
            // Tokens
            $tokens[$key] = $ttl = $data['resetting']['token_time_to_live'];
            $container->setParameter(sprintf('jhv_user.parameter.resetting.%s.token_ttl', $key), $ttl);
        }
        
        // Caso os e-mails estejam definidos, registrar parametro de email
        if (false === empty($emails)) {
            $container->setParameter('jhv_user.parameter.emails', $emails);
        }
        
        // Registrar parâmetro dos tokens de ttl
        $container->setParameter('jhv_user.parameter.tokens_ttl', $tokens);
        
        // Registrar parâmetro de templates
        $container->setParameter('jhv_user.parameter.templates', $templates);
        
        // Caso a disponibilizacao de rotas esteja habilitada, registrar dados para registro do servico
        $container->setParameter('jhv_user.parameter.routes', $routes);
        $loader->load('services/router.yml');

        // Arquivos
        $loader->load('services/services.yml');
        $loader->load('services/security.yml');
        $loader->load('services/mailer.yml');
        $loader->load('services/form.yml');
        $loader->load('services/twig.yml');
        $loader->load('services/listeners.yml');
        $loader->load('services/validator.yml');
    }
    
    /**
     * Criacao de parametros para acesso dos servicos.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     * 
     * @return void
     */
    protected function createContainerParameters(ContainerBuilder $container, array $config)
    {
        $container->setParameter('jhv_user.parameter.translation_domain', $config['default_translation_domain']);
        $container->setParameter('jhv_user.parameter.template.default_layout', $config['templates']['default_layout']);
        $container->setParameter('jhv_user.parameter.template.block_name', $config['templates']['content_block']);

        // Classes (Util)
        $container->setParameter('jhv_user.parameter.class.canonicalizer', $config['classes']['util']['canonicalizer']);
        $container->setParameter('jhv_user.parameter.class.router', $config['classes']['util']['router']);
        $container->setParameter('jhv_user.parameter.class.mailer', $config['classes']['util']['mailer']);
        $container->setParameter('jhv_user.parameter.class.form_factory', $config['classes']['util']['form_factory']);
        $container->setParameter('jhv_user.parameter.class.validator', $config['classes']['util']['validator']);

        // Classes (Managers)
        $container->setParameter('jhv_user.parameter.class.manager.handler', $config['classes']['managers']['handler']);
        $container->setParameter('jhv_user.parameter.class.manager.user', $config['classes']['managers']['user_manager']);
        $container->setParameter('jhv_user.parameter.class.manager.user_helper', $config['classes']['managers']['user_helper']);
        $container->setParameter('jhv_user.parameter.class.manager.group', $config['classes']['managers']['group_manager']);
        $container->setParameter('jhv_user.parameter.class.security.login_manager', $config['classes']['managers']['login_manager']);

        // Classes (Provedores)
        $container->setParameter('jhv_user.parameter.class.provider.username', $config['classes']['providers']['auth_by_username']);
        $container->setParameter('jhv_user.parameter.class.provider.username_or_email', $config['classes']['providers']['auth_by_email']);

        // Classes (Template)
        $container->setParameter('jhv_user.parameter.class.template.manager', $config['templates']['classes']['manager']);
        $container->setParameter('jhv_user.parameter.class.template.renderer', $config['templates']['classes']['renderer']);
    }
    
    /**
     * Criar dependencias referente ao gerenciador.
     * Dependencias estas que passam por criar o servicos necessarios para
     * o funcionamento dos gerenciadores de grupos e usuarios.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $manager Identificador
     * @param array $data Informacoes referentes ao gerenciador
     * 
     * @return void
     */
    protected function createManagerDependencies(ContainerBuilder $container, $manager, array $data)
    {
        $userManagerClass   = (null !== $data['classes']['user_manager']) ?: $container->getParameter('jhv_user.parameter.class.manager.user');
        $groupManagerClass  = (null !== $data['classes']['group_manager']) ?: $container->getParameter('jhv_user.parameter.class.manager.group');
        
        // Criar o gerenciador de usuarios para o modelo de conexao
        $this->createUserManagerDefinition($container, $manager, $userManagerClass, $data['firewall_name'], $data['connection'], $data['classes']['user']);
        
        // Caso o classe de grupo esteja definida, podera criar o gerenciador de grupos
        if (null !== $data['classes']['group']) {
            $this->createGroupManagerDefinition($container, $manager, $groupManagerClass, $data['connection'], $data['classes']['group']);
        }
    }
    
    /**
     * Efetuar a criacao de um novo servico.
     * Este servico é referente ao gerenciador de usuários.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $manager       Identificador do gerenciador
     * @param string $class         Classe do gerenciador
     * @param string $firewall      Nome do firewall
     * @param string $connection    Nome da conexão
     * @param string $entity        Entidade de usuário
     * 
     * @return void
     */
    protected function createUserManagerDefinition(ContainerBuilder $container, $manager, $class, $firewall, $connection, $entity)
    {
        $container
            ->setDefinition(sprintf('jhv_user.manager.%s.user', strtolower($manager)), new Definition(
                $class,
                array(
                    new Reference('jhv_user.manager.user_helper'),
                    $firewall,
                    new Reference(sprintf('doctrine.orm.%s_entity_manager', $connection)),
                    $entity,
                )
            ))
            
            // Definindo como um gerenciador de usuários
            ->addTag('jhv_user.user_manager', array('identifier' => $manager))
                
            // Definicao nao sera publica
            ->setPublic(false)
        ;
    }
    
    /**
     * Efetuar a criacao de um novo servico.
     * Este servico é referente ao gerenciador de grupos.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $manager       Identificador do gerenciador
     * @param string $class         Classe do gerenciador
     * @param string $connection    Nome da conexão
     * @param string $entity        Entidade do grupo
     * 
     * @return void
     */
    protected function createGroupManagerDefinition(ContainerBuilder $container, $manager, $class, $connection, $entity)
    {
        $container
            ->setDefinition(sprintf('jhv_user.manager.%s.group', strtolower($manager)), new Definition(
                $class,
                array(
                    new Reference(sprintf('doctrine.orm.%s_entity_manager', $connection)),
                    $entity,
                )
            ))
            
            // Definindo como um gerenciador de grupos
            ->addTag('jhv_user.group_manager', array('identifier' => $manager))
                
            // Definicao nao sera publica
            ->setPublic(false)
        ;
    }
    
    /**
     * Efetuar a criação de provedores para cada ObjectManager da posição.
     * O provedor servirá para definição no firewall.
     * 
     * Servirá como um atalho para localização do usuário, podendo ser de
     * localização por username ou e-mail.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $identifier Identificador do ObjectManager && Provedor
     * 
     * @return void
     */
    protected function createUserAuthenticationProvider(ContainerBuilder $container, $identifier)
    {
        // Definicao de provedor para busca de usuarios por nome com embasamento no EntityManager
        $container->setDefinition('jhv_user.' . strtolower($identifier) . '_provider.username', new Definition(
            $container->getParameter('jhv_user.parameter.class.provider.username'), array(
                new Reference(sprintf('jhv_user.manager.%s.user', strtolower($identifier))),
            )
        ))
        ->setPublic(false);

        // Definicao de provedor para busca de usuarios por nome ou email com embasamento no EntityManager
        $container->setDefinition('jhv_user.' . strtolower($identifier) . '_provider.username_or_email', new Definition(
            $container->getParameter('jhv_user.parameter.class.provider.username_or_email'), array(
                new Reference(sprintf('jhv_user.manager.%s.user', strtolower($identifier))),
            )
        ))
        ->setPublic(false);
    }
    
    /**
     * Efetuar a criacao do gerenciador e renderizador de templates.
     * 
     * O renderizador e criado baseado no gerenciador, adicionando posteriormente
     * o gerenciador com os respectivos templates.
     * 
     * Ira percorrer e registrar os templates configuradados enviando este para
     * o gerenciador, registrando assim os templates.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $manager
     * @param array $data
     */
    protected function createTemplateDefinition(ContainerBuilder $container, $manager, array $data)
    {
        $templates = $this->getTemplates($data);
        
        // Efetuar criação do serviço de template renderer
        $container
            ->setDefinition(sprintf('jhv_user.template.%s_renderer', $manager), new Definition(
                $container->getParameter('jhv_user.parameter.class.template.renderer'), array(
                    new Reference('twig'),
                    new Definition($container->getParameter('jhv_user.parameter.class.template.manager'), array(
                        $container->getParameter('jhv_user.parameter.template.default_layout'),
                        $container->getParameter('jhv_user.parameter.template.block_name'),
                        $templates,
                    ))
                )
            ))
        ;
    }
    
    /**
     * Localizar os templates relacionados aos dados passados por parametro.
     * 
     * @param array $data
     * @return array
     */
    protected function getTemplates(array $data)
    {
        $templateSectioName = 'templates';
        $sections = array('security', 'registration', 'resetting', 'profile', 'group');
        $templates = array();
        
        // Percorrer sessoes para registro dos templates
        foreach ($sections as $section) {
            // Verificar se existe mesmo o template
            if (isset($data[$section][$templateSectioName])) {
                foreach ($data[$section][$templateSectioName] as $key => $template) {
                    $templates[strtolower($section . '_' . $key)] = $template;
                }
            }
        }
        
        return $templates;
    }
    
    /**
     * Localizar as rotas contidas no conteúdo do gerenciador.
     * 
     * @param array $data
     * @return array
     */
    protected function getRoutes(array $data)
    {
        $routeSectionName = 'routing';
        $sections = array('security', 'registration', 'resetting', 'profile', 'group');
        $routes = array();
        
        foreach ($sections as $section) {
            // Caso a classe de grupo nao esteja ativa, nao rotear
            if ($section === 'group' && null === $data['classes']['group'])
                continue;
            
            if (isset($data[$section][$routeSectionName])) {
                $sectionRoutes = $data[$section][$routeSectionName];
                
                // Percorrer as rotas
                foreach ($sectionRoutes as $key => $value) {
                    $routes[$section][$key] = $value;
                }
            }
        }
        
        return $routes;
    }
    
    /**
     * Efetuar a criacao de fabricas de formulario.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $manager
     * @param array $data
     * 
     * @return void
     */
    protected function createFormFactoryDefinition(ContainerBuilder $container, $manager, array  $data)
    {
        $formSectionName = 'form';
        $sections = array('registration', 'resetting', 'profile', 'group');
        
        foreach ($sections as $section) {
            // Caso a classe de grupo nao esteja ativa, nao rotear
            if ($section === 'group' && null === $data['classes']['group'])
                continue;
            
            // Verificar a existencia da configuracao
            if (isset($data[$section][$formSectionName])) {
                $definition = strtolower(sprintf('jhv_user.form_factory.%s.%s', $section, $manager));
                $formConfig = $data[$section][$formSectionName];
                
                $container->setDefinition($definition, new Definition(
                    $container->getParameter('jhv_user.parameter.class.form_factory'), 
                    array(
                        new Reference('form.factory'),
                        $formConfig['name'],
                        $formConfig['type'],
                        $formConfig['validation_groups'],
                    )
                ));
            }
        }
    }
    
}
