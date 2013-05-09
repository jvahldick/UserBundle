<?php

namespace JHV\Bundle\UserBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use JHV\Bundle\UserBundle\JHVUserEvents;
use JHV\Bundle\UserBundle\Event\GetResponseUserEvent;
use JHV\Bundle\UserBundle\Event\FilterUserResponseEvent;
use JHV\Bundle\UserBundle\Event\FormEvent;

/**
 * ProfileController
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class ProfileController extends UserController
{
    
    /**
     * Este método do controlador tem como objetivo localizar as informações
     * do usuário conectado, listando as informações do mesmo como exibição.
     * 
     * @param   \Symfony\Component\HttpFoundation\Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        $user = $this->getAuthenticatedUser();
        return $this->getTemplateRenderer()->renderResponse('profile_show', array(
            'user' => $user
        ));
    }
    
    /**
     * Método do controlador com intuito de montar informações para exibição
     * do formulário e tratamento para edição do usuário.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedException
     */
    public function editAction(Request $request)
    {
        $manager        = $request->get('manager');
        $userManager    = $this->getUserManager($manager);
        $dispatcher     = $this->getEventDispatcher();
        
        // Localização e checagem do usuário
        $user = $this->getAuthenticatedUser();

        $event = new GetResponseUserEvent($user, $request, $userManager->getFirewallName());
        $dispatcher->dispatch(JHVUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        
        // Criação do formulário de edição do usuário
        $form = $this->getFormFactory()->create('jhv_user_profile_type', $user, array(
            'data_class' => $userManager->getClass()
        ));

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(JHVUserEvents::PROFILE_EDIT_SUCCESS, $event);
                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('jhv_user_profile_show_' . $manager);
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(JHVUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $userManager->getFirewallName(), $response));
                return $response;
            }
        }

        return $this->getTemplateRenderer()->renderResponse('profile_edit', array(
            'form' => $form->createView()
        ));
    }
    
}