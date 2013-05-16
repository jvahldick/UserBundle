<?php

namespace JHV\Bundle\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use JHV\Bundle\UserBundle\JHVUserEvents;
use JHV\Bundle\UserBundle\Event\FilterUserResponseEvent;
use JHV\Bundle\UserBundle\Event\GetResponseUserEvent;
use JHV\Bundle\UserBundle\Event\FormEvent;

/**
 * ChangePasswordController
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class ChangePasswordController extends UserController
{

    /**
     * Efetuar a alteração de senha do usuário.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request)
    {
        $manager        = $request->get('manager');
        $userManager    = $this->getUserManager($manager);
        $user           = $this->getAuthenticatedUser();
        $dispatcher     = $this->getEventDispatcher();
        
        // Despacho de evento de inicialização da alteração de senha
        $event = new GetResponseUserEvent($user, $request, $userManager->getFirewallName());
        $dispatcher->dispatch(JHVUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);
        
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        
        // Criação do usuário para alteração de senha
        $form = $this->container->get(sprintf('jhv_user.form_factory.change_password.%s', $manager))->createForm(array(
            'data_class' => get_class($user)
        ));
        
        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(JHVUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('jhv_user_profile_show_' . $manager);
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(JHVUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $userManager->getFirewallName(), $response));
                return $response;
            }
        }
        
        return $this->getTemplateRenderer()->renderResponse('change_password_change_password', array(
            'form' => $form->createView()
        ));
    }
    
}