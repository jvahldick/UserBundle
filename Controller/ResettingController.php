<?php

namespace JHV\Bundle\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use JHV\Bundle\UserBundle\Event\GetResponseUserEvent;
use JHV\Bundle\UserBundle\JHVUserEvents;
use JHV\Bundle\UserBundle\Event\FormEvent;
use JHV\Bundle\UserBundle\Event\FilterUserResponseEvent;

/**
 * ResettingController
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class ResettingController extends UserController
{
    
    const SESSION_EMAIL = 'jhv_user_send_resetting_email/email';

    /**
     * Efetuar a requisição de uma nova senha.
     * Exibição do formulário.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function requestAction()
    {
        return $this->getTemplateRenderer()->renderResponse('resetting_request');
    }
    
    /**
     * Efetuar verificação do e-mail enviado por formulário.
     * Reiniciar senha de usuário e efetuar envio de e-mail.
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendEmailAction(Request $request)
    {        
        $manager        = $request->get('manager');        
        $userManager    = $this->getUserManager($manager);
        $username       = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $userManager->findUserByUsernameOrEmail($username);
        if (null === $user) {
            return $this->getTemplateRenderer()->renderResponse('resetting_request', array('invalid_username' => $username));
        }

        // No caso da requisição estar dentro de um prazo definido para última requisição.
        if ($user->isPasswordRequestNonExpired($this->container->getParameter('jhv_user.parameter.resetting.'. $manager .'.token_ttl'))) {
            return $this->getTemplateRenderer()->renderResponse('resetting_error', array('error_message' => 'resetting.password_already_requested'));
        }
        
        // Verificação de credenciais e informações para verificar se o usuário está habilitado
        try {
            $this->container->get('security.user_checker')->checkPostAuth($user);
        } catch (AccountStatusException $e) {
            return $this->getTemplateRenderer()->renderResponse('resetting_error', array('error_message' => 'resetting.user_status_error'));
        }

        // Caso a solicitação esteja permitida, criar um token.
        if (null === $user->getConfirmationToken()) {
            $token = base_convert(bin2hex(hash('sha256', uniqid(mt_rand(), true), true)), 16, 36);
            $user->setConfirmationToken($token);
        }

        // Salvar domínio do e-mail em sessão e enviar e-mail ao usuário
        $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));        
        $this->container->get('jhv_user.mailer')->sendResettingEmailMessage($user, $manager);
        $user->setPasswordRequestedAt(new \DateTime());
        $userManager->updateUser($user);

        return new RedirectResponse($this->container->get('router')->generate('jhv_user_resetting_check_email_' . $manager));
    }
    
    /**
     * Exibição de uma mensagem para o usuário informado para o mesmo
     * checar o e-mail pois há informações para reinicialização da senha.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkEmailAction($manager)
    {
        $session    = $this->container->get('session');
        $email      = $session->get(static::SESSION_EMAIL);
        $session->remove(static::SESSION_EMAIL);
        
        if (empty($email)) {
            // sessão do usuário não estabelecida, portanto refazer o processo de requisição
            return new RedirectResponse($this->container->get('router')->generate('jhv_user_resetting_request_' . $manager));
        }

        return $this->getTemplateRenderer()->renderResponse('resetting_check_email', array(
            'email' => $email
        ));
    }
    
    /**
     * Efetuar a reinicialização da senha do usuário.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $manager
     * @param type $token
     * @throws NotFoundHttpException
     */
    public function resetAction(Request $request, $manager, $token)
    {
        $dispatcher = $this->getEventDispatcher();
        $userManager = $this->container->get('jhv_user.manager.handler')->getUserManager($manager);
        $user = $userManager->findUserByConfirmationToken($token);

        // Verificação da existência do usuário
        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request, $userManager->getFirewallName());
        $dispatcher->dispatch(JHVUserEvents::RESETTING_RESET_INITIALIZE, $event);
        
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->container->get(sprintf('jhv_user.form_factory.resetting.%s', $manager))->createForm(array(
            'data_class' => get_class($user),
            'data'       => $user
        ));
        
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(JHVUserEvents::RESETTING_RESET_SUCCESS, $event);

                // Executar atualização do usuário
                $userManager->updateUser($form->getData());
                
                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('jhv_user_profile_show_' . $manager);
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(JHVUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $userManager->getFirewallName(), $response));
                return $response;
            }
        }

        return $this->getTemplateRenderer()->renderResponse('resetting_reset', array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }

    /**
     * Localizar o e-mail truncado para não exibição ao usuário.
     * Remover o possível usuário do e-mail, retornando assim somente o
     * domínio do e-mail.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @return string
     */
    protected function getObfuscatedEmail(UserInterface $user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }
    
}