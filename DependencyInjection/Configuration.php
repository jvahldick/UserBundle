<?php

namespace JHV\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jhv_user');
        
        $this->addDefaultConfigurationSection($rootNode);
        $this->addClassesSection($rootNode);
        $this->addManagersSection($rootNode);
        $this->addRouteSection($rootNode);

        return $treeBuilder;
    }
    
    /**
     * Definição das configurações principais de e-mail
     * 
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
    protected function addDefaultConfigurationSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('default_translation_domain')->defaultValue('JHVUserBundle')->end()
                ->arrayNode('email')
                    ->children()
                        ->scalarNode('from_address')->isRequired()->end()
                        ->scalarNode('from_sender')->isRequired()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
    
    /**
     * Adicionar sessão referente a classes
     * 
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
    protected function addClassesSection(ArrayNodeDefinition $node) 
    {
        $node
            ->children()
                ->arrayNode('classes')
                    ->children()
                        ->scalarNode('manager_handler')->defaultValue('JHV\\Bundle\\UserBundle\\Manager\\Handler')->end()
                        ->scalarNode('user_manager')->defaultValue('JHV\\Bundle\\UserBundle\\Doctrine\\UserManager')->end()
                        ->scalarNode('group_manager')->defaultValue('JHV\\Bundle\\UserBundle\\Doctrine\\GroupManager')->end()
                        ->scalarNode('canonicalizer')->defaultValue('JHV\\Bundle\\UserBundle\\Util\\Canonicalizer')->end()
                        ->scalarNode('user_helper')->defaultValue('JHV\\Bundle\\UserBundle\\Manager\\User\\Helper\\UserHelper')->end()
                        ->scalarNode('router')->defaultValue('JHV\\Bundle\\UserBundle\\Routing\\RouterLoader')->end()
                        ->scalarNode('mailer')->defaultValue('JHV\\Bundle\\UserBundle\\Mailer\\Mailer')->end()
                
                        // Classes relacionados a template
                        ->scalarNode('template_manager')->defaultValue('JHV\\Bundle\\UserBundle\\Template\\Manager')->end()
                        ->scalarNode('template_renderer')->defaultValue('JHV\\Bundle\\UserBundle\\Template\\Renderer')->end()
                
                        // Provedores
                        ->arrayNode('providers')
                            ->children()
                                ->scalarNode('auth_by_username')->defaultValue('JHV\\Bundle\\UserBundle\\Security\\Provider\\UserProvider')->end()
                                ->scalarNode('auth_by_email')->defaultValue('JHV\\Bundle\\UserBundle\\Security\\Provider\\EmailProvider')->end()
                            ->end()
                            ->addDefaultsIfNotSet()
                        ->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
        ;
    }
    
    /**
     * Adicionar sessão de gerenciadores de conexão
     * 
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
    protected function addManagersSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('managers')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('connection')->isRequired()->end()
                            ->scalarNode('firewall_name')->isRequired()->end()
                            ->scalarNode('user_class')->isRequired()->end()
                            ->scalarNode('group_class')->defaultNull()->end()
                            ->scalarNode('user_manager')->defaultNull()->end()
                            ->scalarNode('group_manager')->defaultNull()->end()
                
                            // Adicionar informações quanto ao reset password
                            ->append($this->appendResettingSection())
                            ->append($this->appendTemplateSection())
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
    
    /**
     * Verificar configurações de resetting baseada por manager.
     * 
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    protected function appendResettingSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('resetting');
        
        $node
            ->children()
                // Adicionado TTL do token referente a 1 dia
                ->scalarNode('token_time_to_live')->defaultValue('86400')->end()
                ->arrayNode('email')
                    ->children()
                        ->scalarNode('from_address')->defaultNull()->end()
                        ->scalarNode('from_sender')->defaultNull()->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }
    
    /**
     * Verificar configurações para exibição de templates
     */
    protected function appendTemplateSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('templates');
        
        $node
            ->children()
                // Configurações base
                ->scalarNode('default_layout')->defaultValue('JHVUserBundle::layout.html.twig')->end()
                ->scalarNode('content_block')->defaultValue('jhv_user_content')->end()
                
                // Arquivos de template
                ->arrayNode('files')
                    ->children()
                        // Login
                        ->scalarNode('security_login')->defaultValue('JHVUserBundle:Security:login.html.twig')->end()

                        // Registro templates
                        ->scalarNode('registration_register')->defaultValue('JHVUserBundle:Registration:register.html.twig')->end()
                        ->scalarNode('registration_confirmed')->defaultValue('JHVUserBundle:Registration:confirmed.html.twig')->end()

                        // Profile templates
                        ->scalarNode('profile_edit')->defaultValue('JHVUserBundle:Profile:edit.html.twig')->end()
                        ->scalarNode('profile_show')->defaultValue('JHVUserBundle:Profile:show.html.twig')->end()
                        ->scalarNode('profile_change_password')->defaultValue('JHVUserBundle:Profile:change_password.html.twig')->end()

                        // Reinicialização de credenciais, ou recuperação de senha
                        ->scalarNode('resetting_error')->defaultValue('JHVUserBundle:Resetting:error.html.twig')->end()
                        ->scalarNode('resetting_email')->defaultValue('JHVUserBundle:Email:resetting.html.twig')->end()

                        ->scalarNode('resetting_check_email')->defaultValue('JHVUserBundle:Resetting:email_check.html.twig')->end()
                        ->scalarNode('resetting_request')->defaultValue('JHVUserBundle:Resetting:request.html.twig')->end()
                        ->scalarNode('resetting_reset')->defaultValue('JHVUserBundle:Resetting:reset.html.twig')->end()
                
                        // Grupos
                        ->scalarNode('group_list')->defaultValue('JHVUserBundle:Group:list.html.twig')->end()
                        ->scalarNode('group_create')->defaultValue('JHVUserBundle:Group:create.html.twig')->end()
                        ->scalarNode('group_edit')->defaultValue('JHVUserBundle:Group:edit.html.twig')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }
    
    /**
     * Definição dos roteadores
     * 
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
    protected function addRouteSection(ArrayNodeDefinition $node) 
    {
        $node
            ->children()
                ->arrayNode('routes')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            // Roteamento para o security
                            ->append($this->appendSecurityRouteSection())
                
                            // Roteamento para o registro
                            ->append($this->appendRegistrationRouteSection())
                
                            // Roteamento para perfil do usuário
                            ->append($this->appendProfileRouteSection())
                            
                            // Roteamento para resetar senha
                            ->append($this->appendResettingRouteSection())
                
                            // Roteamento para grupos
                            ->append($this->appendGroupRouteSection())
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
    
    /**
     * Percorre a listagem de roteamentos definidos para segurança.
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendSecurityRouteSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('security');
        
        $node
            ->children()
                ->scalarNode('prefix')->defaultValue('/security')->end()
                
                // Login
                ->arrayNode('login')
                    ->children()
                        ->scalarNode('path')->defaultValue('/login')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Security:login')->end()
                        ->scalarNode('methods')->defaultValue('GET')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Checagem
                ->arrayNode('check')
                    ->children()
                        ->scalarNode('path')->defaultValue('/check')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Security:check')->end()
                        ->scalarNode('methods')->defaultValue('POST')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Logout
                ->arrayNode('logout')
                    ->children()
                        ->scalarNode('path')->defaultValue('/logout')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Security:logout')->end()
                        ->scalarNode('methods')->defaultValue('GET')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }
    
    /**
     * Verificação e registro do nodo referente ao registro
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendRegistrationRouteSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('registration');
        
        $node
            ->children()
                ->scalarNode('prefix')->defaultValue('/registration')->end()
                
                // Registro
                ->arrayNode('register')
                    ->children()
                        ->scalarNode('path')->defaultValue('/register')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Registration:register')->end()
                        ->scalarNode('methods')->defaultNull()->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Confirmação de registro
                ->arrayNode('confirmed')
                    ->children()
                        ->scalarNode('path')->defaultValue('/confirmed')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Registration:confirmed')->end()
                        ->scalarNode('methods')->defaultValue('GET')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }
    
    /**
     * Verificação e registro do nodo referente ao perfil
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendProfileRouteSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('profile');
        
        $node
            ->children()
                ->scalarNode('prefix')->defaultValue('/profile')->end()
                ->arrayNode('edit')
                    ->children()
                        ->scalarNode('path')->defaultValue('/edit')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Profile:edit')->end()
                        ->scalarNode('methods')->defaultNull()->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                ->arrayNode('show')
                    ->children()
                        ->scalarNode('path')->defaultValue('/show')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Profile:show')->end()
                        ->scalarNode('methods')->defaultValue('GET')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                ->arrayNode('change_password')
                    ->children()
                        ->scalarNode('path')->defaultValue('/change-password')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:ChangePassword:changePassword')->end()
                        ->scalarNode('methods')->defaultValue('GET|POST')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }
    
    /**
     * Roteamento para sessão de redefinir senha
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendResettingRouteSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('resetting');
        
        $node
            ->children()
                ->scalarNode('prefix')->defaultValue('/resetting')->end()
                
                // Solicitação de reinialização da senha
                ->arrayNode('request')
                    ->children()
                        ->scalarNode('path')->defaultValue('/request')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Resetting:request')->end()
                        ->scalarNode('methods')->defaultValue('GET')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Envio de e-mail para nova senha
                ->arrayNode('send_email')
                    ->children()
                        ->scalarNode('path')->defaultValue('/email/send')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Resetting:sendEmail')->end()
                        ->scalarNode('methods')->defaultValue('POST')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Verificação do e-mail
                ->arrayNode('check_email')
                    ->children()
                        ->scalarNode('path')->defaultValue('/email/check')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Resetting:checkEmail')->end()
                        ->scalarNode('methods')->defaultValue('GET')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Reinicialização da senha
                ->arrayNode('reset')
                    ->children()
                        ->scalarNode('path')->defaultValue('/reset/{token}')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Resetting:reset')->end()
                        ->scalarNode('methods')->defaultValue('GET|POST')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }
    
    /**
     * Roteamento para sessão de grupos
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendGroupRouteSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('groups');
        
        $node
            ->children()
                ->scalarNode('enabled')->defaultFalse()->end()
                ->scalarNode('prefix')->defaultValue('/groups')->end()
                
                // Listagem de grupos
                ->arrayNode('list')
                    ->children()
                        ->scalarNode('path')->defaultValue('/list')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Group:list')->end()
                        ->scalarNode('methods')->defaultValue('GET')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Criação de grupos
                ->arrayNode('create')
                    ->children()
                        ->scalarNode('path')->defaultValue('/create')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Group:create')->end()
                        ->scalarNode('methods')->defaultValue('GET|POST')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()

                // Edição de grupos
                ->arrayNode('edit')
                    ->children()
                        ->scalarNode('path')->defaultValue('/{groupId}/edit')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Group:edit')->end()
                        ->scalarNode('methods')->defaultValue('GET|POST')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()

                // Exclusão de grupos
                ->arrayNode('delete')
                    ->children()
                        ->scalarNode('path')->defaultValue('/{groupId}/delete')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Group:delete')->end()
                        ->scalarNode('methods')->defaultValue('GET')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }
    
}
