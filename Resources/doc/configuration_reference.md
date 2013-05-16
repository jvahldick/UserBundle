Configurações de Referência
===========================

Segue abaixo as configurações possíveis para utilização do Bundle.

``` yaml
# app/config/config.yml

### Especificação de usuários
jhv_users:
    default_translation_domain  : "JHVUserBundle"
    enabled_routing             : false

    ### Configurações quanto a e-mail
    email:
        from_address    : # required
        from_sender     : # required

    ### Definição as classes gerais
    classes:
        ### Classes de gerenciadores
        managers:
            handler         : "JHV\\Bundle\\UserBundle\\Manager\\Handler"
            user_manager    : "JHV\\Bundle\\UserBundle\\Doctrine\\UserManager"
            user_helper     : "JHV\\Bundle\\UserBundle\\Manager\\User\\Helper\\UserHelper"
            group_manager   : "JHV\\Bundle\\UserBundle\\Doctrine\\GroupManager"

        ### Util
        util:
            form_factory    : "JHV\\Bundle\\UserBundle\\Form\\Factory\\FormFactory"
            canonicalizer   : "JHV\\Bundle\\UserBundle\\Util\\Canonicalizer"
            router          : "JHV\\Bundle\\UserBundle\\Routing\\RouterLoader"
            mailer          : "JHV\\Bundle\\UserBundle\\Mailer\\Mailer"


    ### Configurações referentes aos templates
    templates:
        default_layout 	: "JHVUserBundle::layout.html.twig"
        content_block 	: "jhv_user_content"

        classes:
            manager     : "JHV\\Bundle\\UserBundle\\Template\\Manager"
            renderer    : "JHV\\Bundle\\UserBundle\\Template\\Renderer"

    ### Configurações referentes aos gerenciadores de conexão
    managers:
        default:
            ### Conexão e firewall
            connection      : # required
            firewall_name   : # required

            ### Configurações das classes das entidades e gerenciadores da conexão definida
            classes:
                user 		: # required
                group 		: # required
                user_manager 	: ~
                group_manager 	: ~

            ### Configurações referente a segurança
            security:
                classes:
                    manager : "JHV\\Bundle\\UserBundle\\Security\\LoginManager"

                ### Templates de segurança
                templates:
                    login   : "JHVUserBundle:Security:login.html.twig"

                ### Roteamento de segurança
                routing:
                    prefix : "/security"

                    ### Efetuar login
                    login:
                        path        : "/login"
                        controller  : "JHVUserBundle:Security:login"
                        methods     : "GET"

                    ### Verificação do login
                    check:
                        path        : "/check"
                        controller  : "JHVUserBundle:Security:check"
                        methods     : "POST"

                    ### Efetuar Logout
                    logout:
                        path        : "/logout"
                        controller  : "JHVUserBundle:Security:logout"
                        methods     : "GET"



            ### Configurações para registro de usuários
            registration:
                ### Configurações de formulário para o registro de usuários
                form:
                    name                : "jhv_user_registration_form"
                    type 	 	        : "jhv_user_registration_type"
                    validation_groups 	: ["Registration", "Default"]

                ### Configurações de templates para o registro de usuários
                templates:
                    register            : "JHVUserBundle:Registration:register.html.twig"
                    register_confirmed 	: "JHVUserBundle:Registration:confirmed.html.twig"

                ### Roteamento do registro
                routing:
                    prefix : "/registration"

                    ### Registro de usuários
                    register:
                        path        : "/register"
                        controller  : "JHVUserBundle:Registration:register"
                        methods     : "GET|POST"

                    ### Confirmação
                    confirmed:
                        path        : "/register/success"
                        controller  : "JHVUserBundle:Registration:confirmed"
                        methods     : "GET"



            ### Configurações para refazer as credenciais do usuário
            resetting:
                token_time_to_live: 86400 # equivalente a 24 horas

                ### Configurações de e-mail, caso seja em branco localizará do definido anteriormente
                email:
                    from_address    : ~
                    from_sender     : ~

                ### Configurações referente aos templates da reinicialização da senha
                form:
                    name                : "jhv_user_resetting_form"
                    type                : "jhv_user_resetting_type"
                    validation_groups 	: ["ResetPassword", "Default"]

                ### Configurações de template a reinicialização de credenciais
                templates:
                    request     : "JHVUserBundle:Resetting:email_check.html.twig" 	# Requisição da senha
                    reset 	    : "JHVUserBundle:Resetting:reset.html.twig" 		# Reinicialização da senha
                    error 	    : "JHVUserBundle:Resetting:error.html.twig" 		# Erro na localização do usuário
                    email 	    : "JHVUserBundle:Email:resetting.html.twig" 		# E-mail enviado ao usuário
                    check_email : "JHVUserBundle:Resetting:email_check.html.twig" 	# Template informativo solicitando para o usuário verificar o e-mail

                ### Roteamento do registro
                routing:
                    prefix : "/resetting"

                    ### Requisição de recuperação de senha
                    request:
                        path 		: "/request"
                        controller 	: "JHVUserBundle:Resetting:request"
                        methods 	: "GET"

                    ### Envio do e-mail
                    send_email:
                        path 		: "/email/send"
                        controller 	: "JHVUserBundle:Resetting:sendEmail"
                        methods 	: "POST"

                    ### Mensagem de verificação de e-mail para recuperação
                    check_email:
                        path 		: "/email/check"
                        controller 	: "JHVUserBundle:Resetting:checkEmail"
                        methods 	: "GET"

                    ### Reinicialização
                    reset:
                        path 		: "/reset/{token}"
                        controller 	: "JHVUserBundle:Resetting:reset"
                        methods 	: "GET|POST"


            ### Configurações referentes aos grupos
            group:
                ### Configurações referente aos templates de grupos
                form:
                    name                : "jhv_user_group_form"
                    type                : "jhv_user_group_type"
                    validation_groups 	: ["Registration", "Default"]


                ### Configurações referentes aos templates dos grupos
                templates:
                    create  : "JHVUserBundle:Group:create.html.twig"
                    edit    : "JHVUserBundle:Group:edit.html.twig"
                    list    : "JHVUserBundle:Group:list.html.twig"

                ### Roteamento do grupo
                routing:
                    prefix : "/groups"

                    ### Listagem de grupos
                    list:
                        path 		: "/list"
                        controller 	: "JHVUserBundle:Group:list"
                        methods 	: "GET"

                    ### Criação de grupos
                    create:
                        path 		: "/create"
                        controller 	: "JHVUserBundle:Group:create"
                        methods 	: "GET|POST"

                    ### Edição de grupos
                    edit:
                        path 		: "/{groupId}/edit"
                        controller 	: "JHVUserBundle:Group:create"
                        methods 	: "GET|POST"

                    ### Exclusão de grupo
                    edit:
                        path 		: "/{groupId}/delete"
                        controller 	: "JHVUserBundle:Group:delete"
                        methods 	: "GET"


            ### Configurações para perfil de usuário
            profile:
                ### Configurações de formulário para os perfis
                form:
                    name                : "jhv_user_profile_form"
                    type 		        : "jhv_user_profile_type"
                    validation_groups 	: ["Profile", "Default"]

                ### Templates para perfis de usuário
                templates:
                    edit 		: "JHVUserBundle:Profile:edit.html.twig"
                    show 		: "JHVUserBundle:Profile:show.html.twig"

                ### Roteamento de perfil
                routing:
                    prefix : "/profile"

                    ### Editar
                    edit:
                        path 		: "/edit"
                        controller 	: "JHVUserBundle:Profile:edit"
                        methods 	: "GET|POST"

                    ### Exibir
                    show:
                        path 		: "/show"
                        controller 	: "JHVUserBundle:Profile:show"
                        methods 	: "GET"

            ### Configurações para recuperação de senha
            change_password:
                ### Configurações de formulário para os perfis
                form:
                    name                : "jhv_user_change_password_form"
                    type 		        : "jhv_user_change_password_type"
                    validation_groups 	: ["ChangePassword", "Default"]

                ### Templates para perfis de usuário
                templates:
                    change_password     : "JHVUserBundle:Profile:change_password.html.twig"

                ### Roteamento de perfil
                routing:
                    prefix : "/profile"

                    ### Modificação de senha
                    change_password:
                        path 		: "/change-password"
                        controller 	: "JHVUserBundle:ChangePassword:changePassword"
                        methods 	: "GET|POST"
```