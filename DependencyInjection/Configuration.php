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
        
        // Configuracoes gerais
        $this->addDefaultConfigurationSection($rootNode);
        $this->addClassesSection($rootNode);
        $this->addTemplatesSection($rootNode);
        
        // Configuracoes especificas por sessao
        $this->addManagersSection($rootNode);

        return $treeBuilder;
    }
    
    /**
     * Definicao das configuracoes principais.
     * 
     * Configuracoes:
     * - Definicao do dominio para traducao (arquivo)
     * - Definicao para envio de e-mails
     * - Verificar criacao ou nao de roteamentos
     * 
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
    protected function addDefaultConfigurationSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('default_translation_domain')->defaultValue('JHVUserBundle')->end()
                ->scalarNode('enabled_routing')->defaultFalse()->end()
                
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
     * Definicao das classes dos objetos.
     * - Classes de gerenciamento das entidades
     * - Classes de utilidades
     * - Provedores de acesso
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
                        // Referente ao gerenciamento de entidades e banco de dados
                        ->arrayNode('managers')
                            ->children()
                                ->scalarNode('login_manager')->defaultValue('JHV\\Bundle\\UserBundle\\Security\\LoginManager')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('handler')->defaultValue('JHV\\Bundle\\UserBundle\\Manager\\Handler')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('user_manager')->defaultValue('JHV\\Bundle\\UserBundle\\Doctrine\\UserManager')->cannotBeEmpty()->isRequired()->end()
                                ->scalarNode('user_helper')->defaultValue('JHV\\Bundle\\UserBundle\\Manager\\User\\Helper\\UserHelper')->cannotBeEmpty()->isRequired()->end()
                                ->scalarNode('group_manager')->defaultValue('JHV\\Bundle\\UserBundle\\Doctrine\\GroupManager')->cannotBeEmpty()->end()
                            ->end()
                            ->addDefaultsIfNotSet()
                        ->end()
                
                        // Uteis
                        ->arrayNode('util')
                            ->children()
                                ->scalarNode('form_factory')->defaultValue('JHV\\Bundle\\UserBundle\\Form\\Factory\\FormFactory')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('canonicalizer')->defaultValue('JHV\\Bundle\\UserBundle\\Util\\Canonicalizer')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('router')->defaultValue('JHV\\Bundle\\UserBundle\\Routing\\RouterLoader')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('mailer')->defaultValue('JHV\\Bundle\\UserBundle\\Mailer\\Mailer')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('validator')->defaultValue('JHV\\Bundle\\UserBundle\\Validator\\Initializer')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                            ->addDefaultsIfNotSet()
                        ->end()
                
                        // Provedores
                        ->arrayNode('providers')
                            ->children()
                                ->scalarNode('auth_by_username')->defaultValue('JHV\\Bundle\\UserBundle\\Security\\Provider\\UserProvider')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('auth_by_email')->defaultValue('JHV\\Bundle\\UserBundle\\Security\\Provider\\EmailProvider')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                            ->addDefaultsIfNotSet()
                        ->end()
                    ->end()
                
                    // Definir padroes caso nao hajam definicoes especificas
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
        ;
    }
    
    /**
     * Definicao das configuracoes principais quanto a template.
     * - Definicao do nome do template principal
     * - Definicao do nome do bloco de exibicao de conteudo
     * - Definicao de classes para renderizacao e gerencimaneto de templates
     * 
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
    protected function addTemplatesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('templates')
                    ->children()
                        ->scalarNode('default_layout')->defaultValue('JHVUserBundle::layout.html.twig')->cannotBeEmpty()->end()
                        ->scalarNode('content_block')->defaultValue('jhv_user_content')->end()
                
                        ->arrayNode('classes')
                            ->children()
                                ->scalarNode('manager')->defaultValue('JHV\\Bundle\\UserBundle\\Template\\Manager')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('renderer')->defaultValue('JHV\\Bundle\\UserBundle\\Template\\Renderer')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                            ->addDefaultsIfNotSet()
                        ->end()
                    ->end()
                
                    // Definir padroes caso nao hajam definicoes especificas
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
        ;
    }
    
    /**
     * Sessao referente aos gerenciadores das entidades e conexoes.
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
                            // Nome do firewall e conexao
                            ->scalarNode('connection')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('firewall_name')->isRequired()->cannotBeEmpty()->end()

                            // Classes
                            ->arrayNode('classes')
                                ->children()
                                    ->scalarNode('user')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('group')->defaultNull()->end()
                                    ->scalarNode('user_manager')->defaultNull()->end()
                                    ->scalarNode('group_manager')->defaultNull()->end()
                                ->end()
                            ->end()
                
                            // Adicionar configuracoes especificas por sessao
                            ->append($this->appendSecuritySection())
                            ->append($this->appendRegistrationSection())
                            ->append($this->appendResettingSection())
                            ->append($this->appendProfileSection())
                            ->append($this->appendChangePasswordSection())
                            ->append($this->appendGroupSection())
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
    
    /**
     * Definicoes da sessao de seguranca.
     * Este metodo ira percorrer as configuracoes da sessao efetuando
     * desta forma a validacao dos dados.
     * 
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendSecuritySection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('security');
        
        $node
            ->children()
                // Templates que serao utilizados no controlador da seguranca
                ->arrayNode('templates')
                    ->children()
                        ->scalarNode('login')->defaultValue('JHVUserBundle:Security:login.html.twig')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Roteamento
                ->append($this->appendSecurityRouteSection())
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }
    
    /**
     * Efetuar o registro nos nodos da sessao de registro.
     * Este metodo ira percorrer as configuracoes da sessao efetuando
     * desta forma a validacao dos dados.
     * 
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    protected function appendRegistrationSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('registration');
        
        $node
            ->children()
                // Formulario
                ->arrayNode('form')
                    ->children()
                        ->scalarNode('name')->defaultValue('jhv_user_registration_form')->isRequired()->end()
                        ->scalarNode('type')->defaultValue('jhv_user_registration_type')->end()
                        ->arrayNode('validation_groups')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('Registration', 'Default'))
                        ->end()
                    ->end()

                    ->addDefaultsIfNotSet()
                ->end()
                
                // Templates para registro
                ->arrayNode('templates')
                    ->children()
                        ->scalarNode('register')->defaultValue('JHVUserBundle:Registration:register.html.twig')->end()
                        ->scalarNode('register_confirmed')->defaultValue('JHVUserBundle:Registration:confirmed.html.twig')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Roteamento para sessao de registro
                ->append($this->appendRegistrationRouteSection())
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }
    
    /**
     * Configuracoes e embasamento da recuperacao de senha.
     * Este metodo ira percorrer as configuracoes da sessao efetuando
     * desta forma a validacao dos dados.
     * 
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
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
                
                // Formulario
                ->arrayNode('form')
                    ->children()
                        ->scalarNode('name')->defaultValue('jhv_user_resetting_form')->isRequired()->end()
                        ->scalarNode('type')->defaultValue('jhv_user_resetting_type')->end()
                        ->arrayNode('validation_groups')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('ResetPassword', 'Default'))
                        ->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Templates para recuperacao de senha
                ->arrayNode('templates')
                    ->children()
                        // Reinicializacao de credenciais, ou recuperacao de senha
                        ->scalarNode('request')->defaultValue('JHVUserBundle:Resetting:request.html.twig')->end()
                        ->scalarNode('reset')->defaultValue('JHVUserBundle:Resetting:reset.html.twig')->end()

                        ->scalarNode('error')->defaultValue('JHVUserBundle:Resetting:error.html.twig')->end()
                        ->scalarNode('email')->defaultValue('JHVUserBundle:Email:resetting.html.twig')->end()
                        ->scalarNode('check_email')->defaultValue('JHVUserBundle:Resetting:email_check.html.twig')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Roteamento
                ->append($this->appendResettingRouteSection())
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }
    
    /**
     * Configuracao e embasamento na criacao de grupos de usuarios.
     * Este metodo ira percorrer as configuracoes da sessao efetuando
     * desta forma a validacao dos dados.
     * 
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendGroupSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('group');
        
        $node
            ->children()                
                // Informacoes de formulario
                ->arrayNode('form')
                    ->children()
                        ->scalarNode('name')->defaultValue('jhv_user_group_form')->end()
                        ->scalarNode('type')->defaultValue('jhv_user_group_type')->end()
                        ->arrayNode('validation_groups')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('Registration', 'Default'))
                        ->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Templates relacionados aos grupos de usuarios
                ->arrayNode('templates')
                    ->children()
                        ->scalarNode('create')->defaultValue('JHVUserBundle:Group:create.html.twig')->end()
                        ->scalarNode('edit')->defaultValue('JHVUserBundle:Group:edit.html.twig')->end()
                        ->scalarNode('list')->defaultValue('JHVUserBundle:Group:list.html.twig')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Adicionar roteamento para grupos
                ->append($this->appendGroupRouteSection())
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }
    
    /**
     * Processamento dos nodos referentes ao perfil do usuario.
     * Este metodo ira percorrer as configuracoes da sessao efetuando
     * desta forma a validacao dos dados.
     * 
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendProfileSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('profile');
        
        $node
            ->children()
                // Nomenclatura aos formularios
                ->arrayNode('form')
                    ->children()
                        ->scalarNode('name')->defaultValue('jhv_user_profile_form')->isRequired()->end()
                        ->scalarNode('type')->defaultValue('jhv_user_profile_type')->end()
                        ->arrayNode('validation_groups')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('Profile', 'Default'))
                        ->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Templates referentes ao peril do usuario
                ->arrayNode('templates')
                    ->children()
                        ->scalarNode('edit')->defaultValue('JHVUserBundle:Profile:edit.html.twig')->end()
                        ->scalarNode('show')->defaultValue('JHVUserBundle:Profile:show.html.twig')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Roteamento
                ->append($this->appendProfileRouteSection())
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }

    /**
     * Verificar as definicoes da configuracao referente a reinicilizacao de senha.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendChangePasswordSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('change_password');

        $node
            ->children()
                // Formulário
                ->arrayNode('form')
                    ->children()
                        ->scalarNode('name')->defaultValue('jhv_user_change_password_form')->isRequired()->end()
                        ->scalarNode('type')->defaultValue('jhv_user_change_password_type')->end()
                        ->arrayNode('validation_groups')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('ChangePassword', 'Default'))
                        ->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()

                // Templates
                ->arrayNode('templates')
                    ->children()
                        ->scalarNode('change_password')->defaultValue('JHVUserBundle:Profile:change_password.html.twig')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()

                // Roteamento
                ->append($this->appendChangePasswordRouteSection())
            ->end()
            ->addDefaultsIfNotSet()
        ;

        return $node;
    }
    
    /**
     * Percorre a listagem de roteamentos definidos para seguranca.
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendSecurityRouteSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('routing');
        
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
     * Verificacao e registro do nodo referente ao registro
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendRegistrationRouteSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('routing');
        
        $node
            ->children()
                ->scalarNode('prefix')->defaultValue('/registration')->end()
                
                // Registro
                ->arrayNode('register')
                    ->children()
                        ->scalarNode('path')->defaultValue('/register')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Registration:register')->end()
                        ->scalarNode('methods')->defaultNull('GET|POST')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Confirmacao de registro
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
     * Verificacao e registro do nodo referente ao perfil
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendProfileRouteSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('routing');
        
        $node
            ->children()
                ->scalarNode('prefix')->defaultValue('/profile')->end()
                
                ->arrayNode('edit')
                    ->children()
                        ->scalarNode('path')->defaultValue('/edit')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Profile:edit')->end()
                        ->scalarNode('methods')->defaultNull('GET|POST')->end()
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
            ->end()
            ->addDefaultsIfNotSet()
        ;
        
        return $node;
    }

    /**
     * Verificacao e registro do nodo referente a reinicializacao de credenciais
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendChangePasswordRouteSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('routing');

        $node
            ->children()
                ->scalarNode('prefix')->defaultValue('/profile')->end()

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
     * Roteamento para sessao de recuperacao de senha.
     * 
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendResettingRouteSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('routing');
        
        $node
            ->children()
                ->scalarNode('prefix')->defaultValue('/resetting')->end()
                
                // Solicitacao de recuperacao da senha
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
                
                // Verificacao do e-mail
                ->arrayNode('check_email')
                    ->children()
                        ->scalarNode('path')->defaultValue('/email/check')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Resetting:checkEmail')->end()
                        ->scalarNode('methods')->defaultValue('GET')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
                
                // Reinicializacao da senha
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
     * Roteamento para sessao de grupos.
     * Percorrer e validar as configuracoes referentes ao roteamento de grupos.
     * 
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function appendGroupRouteSection()
    {
        $builder    = new TreeBuilder();
        $node       = $builder->root('routing');
        
        $node
            ->children()
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
                
                // Criacao de grupos
                ->arrayNode('create')
                    ->children()
                        ->scalarNode('path')->defaultValue('/create')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Group:create')->end()
                        ->scalarNode('methods')->defaultValue('GET|POST')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()

                // Edicao de grupos
                ->arrayNode('edit')
                    ->children()
                        ->scalarNode('path')->defaultValue('/{groupId}/edit')->end()
                        ->scalarNode('controller')->defaultValue('JHVUserBundle:Group:edit')->end()
                        ->scalarNode('methods')->defaultValue('GET|POST')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()

                // Exclusao de grupos
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
