<?php

namespace JHV\Bundle\UserBundle;

/**
 * JHVUserEvents
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
final class JHVUserEvents
{
    
    /**
     * O evento CHANGE_PASSWORD_INITIALIZE ocorre quando o processo alteração de senha é inicializado.
     *
     * Este evento permite que você modifique os valores padrão do usuário.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\GetResponseUserEvent.
     */
    const CHANGE_PASSWORD_INITIALIZE = 'jhv_user.change_password.initialize';
    
    /**
     * O evento CHANGE_PASSWORD_SUCCESS ocorre na alteração realizada da senha.
     * 
     * O evento permite alterar o componente de resposta do browser.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\FormEvent.
     */
    const CHANGE_PASSWORD_SUCCESS = 'jhv_user.change_password.success';
    
    /**
     * O evento CHANGE_PASSWORD_COMPLETED é executado ao completar a alteração de senha.
     * 
     * O evento permite alterar o componente de resposta do browser.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\FilterUserResponseEvent.
     */
    const CHANGE_PASSWORD_COMPLETED = 'jhv_user.change_password.completed';
    
    /**
     * O evento GROUP_CREATE_INITIALIZE é executado na inicialização da criação de um grupo.
     * 
     * Este evento permite modificações pré form bind.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\GroupEvent.
     */
    const GROUP_CREATE_INITIALIZE = 'jhv_user.group.create.initialize';
    
    /**
     * O evento GROUP_CREATE_SUCCESS é executado no formulário validado.
     * 
     * O evento permite alterar o componente de resposta do browser.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\FormEvent.
     */
    const GROUP_CREATE_SUCCESS = 'jhv_user.group.create.success';

    /**
     * O Evento GROUP_CREATE_COMPLETED é executado ao finalizar o processo de criação de grupos.
     *
     * O evento permite alterar o componente de resposta do browser.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\FilterGroupResponseEvent.
     */
    const GROUP_CREATE_COMPLETED = 'jhv_user.group.create.completed';

    /**
     * O Evento GROUP_DELETE_COMPLETED é executado ao finalizar o processo de exclusão de um grupo.
     *
     * O evento permite alterar o componente de resposta do browser.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\FilterGroupResponseEvent.
     */
    const GROUP_DELETE_COMPLETED = 'jhv_user.group.delete.completed';

    /**
     * O evento GROUP_EDIT_INITIALIZE é executado na inicialização da edição de um grupo.
     * 
     * Este evento permite modificações pré form bind.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\GetResponseGroupEvent.
     */
    const GROUP_EDIT_INITIALIZE = 'jhv_user.group.edit.initialize';

    /**
     * O evento GROUP_EDIT_SUCCESS é executado na validação dos dados de edição de um grupo.
     *
     * O evento permite alterar o componente de resposta do browser.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\FormEvent.
     */
    const GROUP_EDIT_SUCCESS = 'jhv_user.group.edit.success';

    /**
     * O evento GROUP_EDIT_COMPLETED é executado na conclusão da edição de um grupo.
     * 
     * O evento permite alteração a resposta do servidor.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\FilterGroupResponseEvent.
     */
    const GROUP_EDIT_COMPLETED = 'jhv_user.group.edit.completed';
    
    /**
     * O evento PROFILE_EDIT_INITIALIZE ocorre quando o processo de edição do perfil é inicializado.
     *
     * Este evento permite que você modifique os valores padrão do usuário.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\GetResponseUserEvent.
     */
    const PROFILE_EDIT_INITIALIZE = 'jhv_user.profile.edit.initialize';
    
    /**
     * O evento PROFILE_EDIT_SUCCESS é chamado na submissão de um formulário.
     *
     * O evento permite alterar o componente de resposta do browser.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\FormEvent.
     */
    const PROFILE_EDIT_SUCCESS = 'jhv_user.profile.edit.success';
    
    /**
     * O evento PROFILE_EDIT_COMPLETED é executado após os dados de perfil serem salvos.
     *
     * O evento permite alterar o componente de resposta do browser.
     * O ouvinte do evento receberá instancia de JHV\Bundle\UserBundle\Event\FilterUserResponseEvent.
     */
    const PROFILE_EDIT_COMPLETED = 'jhv_user.profile.edit.completed';
       
    
    /**
     * O evento REGISTRATION_INITIALIZE é executado ao inicializar um novo registro.
     * 
     * Evento permite modificar valores padrões após bind do formulário.
     * O ouvinte evento receberá o evento JHV\Bundle\UserBundle\Event\UserEvent.
     */
    const REGISTRATION_INITIALIZE = 'jhv_user.registration.initialize';
    
    /**
     * O evento REGISTRATION_SUCCESS ocorre ao usuário efetuar o registro.
     * 
     * Este evento permite modificar a resposta enviada ao usuário.
     * O evento receberá instancia de JHV\Bundle\UserBundle\Event\FormEvent
     */
    const REGISTRATION_SUCCESS = 'jhv_user.registration.success';
    
    /**
     * O evento REGISTRATION_CONFIRMED ocorre na confirmação do registro da conta.
     * 
     * Neste evento será permitido acessar a resposta que será enviada.
     * O evento receberá instancia de JHV\Bundle\UserBundle\Event\FilterUserResponseEvent
     */
    const REGISTRATION_CONFIRMED = 'jhv_user.registration.confirmed';
    
    /**
     * O evento REGISTRATION_COMPLETED será executado na confirmação do registro do usuário.
     * 
     * Neste evento será permitido acesso de qual resposta será enviada.
     * O método do evento receberá instancia de JHV\Bundle\UserBundle\Event\FilterUserResponseEvent
     */
    const REGISTRATION_COMPLETED = 'jhv_user.registration.completed';
    
    /**
     * O evento RESETTING_RESET_INITIALIZE é executo no momento de inicialização do processo de reinicialização das credenciais.
     * 
     * O evento permite definir uma resposta ao invés de utilizar uma padrão.
     * O evento recebe como parâmetro uma instancia de JHV\Bundle\UserBundle\Event\FormEvent.
     */
    const RESETTING_RESET_INITIALIZE = 'jhv_user.resetting.initialize';
    
    /**
     * O evento RESETTING_RESET_SUCCESS é executo no momento de finalização do processdo de reinicialização de credenciais.
     * 
     * Este evento permite que você acesse a resposta que será enviada.
     * O evento recebe como parâmetro uma instancia de JHV\Bundle\UserBundle\Event\FilterUserResponseEvent.
     */
    const RESETTING_RESET_SUCCESS = 'jhv_user.resetting.sucess';
    
    /**
     * O evento RESETTING_RESET_COMPLETED após salvar os dados de reinicialização de credenciais.
     *
     * O evento permite acessar a resposta que será enviada.
     * O evento receberá instancia de JHV\Bundle\UserBundle\Event\FilterUserResponseEvent.
     */
    const RESETTING_RESET_COMPLETED = 'jhv_user.resetting.completed';
    
    /**
     * O evento SECURITY_IMPLICIT_LOGIN é executado na autenticação manual.
     * 
     * This event allows you to access the response which will be sent.
     * The event listener method receives a FOS\UserBundle\Event\UserEvent instance.
     */
    const SECURITY_IMPLICIT_LOGIN = 'jhv_user.security.implicit_login';
    
}