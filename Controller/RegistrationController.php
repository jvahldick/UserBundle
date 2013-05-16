<?php

namespace JHV\Bundle\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JHV\Bundle\UserBundle\Event\UserEvent;
use JHV\Bundle\UserBundle\Event\FormEvent;
use JHV\Bundle\UserBundle\Event\FilterUserResponseEvent;
use JHV\Bundle\UserBundle\JHVUserEvents;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * RegistrationController
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class RegistrationController extends ContainerAware
{
    
    /**
     * Efetuar o registro de usuários
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $manager Nome do gerenciador
     */
    public function registerAction(Request $request)
    {
        $manager        = $request->get('manager');
        $dispatcher     = $this->container->get('event_dispatcher');
        $handler        = $this->container->get('jhv_user.manager.handler');
        $userManager    = $handler->getUserManager($manager);
        
        /* @var $formFactory \JHV\Bundle\UserBundle\Form\Factory\FormFactory */
        $formFactory = $this->container->get(sprintf('jhv_user.form_factory.registration.%s', $manager));
        
        $user = $userManager->createUser();
        $dispatcher->dispatch(JHVUserEvents::REGISTRATION_INITIALIZE, new UserEvent($user, $request, $userManager->getFirewallName()));

        $form = $formFactory->createForm(array(
            'data_class' => $userManager->getClass()
        ));
        
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $user = $form->getData();
                
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(JHVUserEvents::REGISTRATION_SUCCESS, $event);

                // Efetuar as alterações ao usuário
                $userManager->updateUser($user);
                
                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('jhv_user_registration_confirmed_' . $manager);
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(JHVUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $userManager->getFirewallName(), $response));
                return $response;
            }
        }

        return $this->container->get(sprintf('jhv_user.template.%s_renderer', $manager))->renderResponse('registration_register', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Informa ao usuário que sua conta foi criada com sucesso.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function confirmedAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        // Checar autorização de usuário
        $this->container->get('security.user_checker')->checkPostAuth($user);

        return $this->container->get('templating')->renderResponse('JHVUserBundle:Registration:confirmed.html.twig', array(
            'user' => $user,
        ));
    }
    
}