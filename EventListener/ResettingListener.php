<?php

namespace JHV\Bundle\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use JHV\Bundle\UserBundle\JHVUserEvents;
use JHV\Bundle\UserBundle\Event\GetResponseUserEvent;
use JHV\Bundle\UserBundle\Event\FormEvent;

/**
 * ResettingListener
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class ResettingListener implements EventSubscriberInterface
{
    
    protected $router;
    protected $tokensTtl;
    
    public function __construct(UrlGeneratorInterface $router, array $tokensTtl)
    {
        $this->router = $router;
        $this->tokensTtl = $tokensTtl;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            JHVUserEvents::RESETTING_RESET_INITIALIZE   => 'onResettingResetInitialize',
            JHVUserEvents::RESETTING_RESET_SUCCESS      => 'onResettingResetSuccess'
        );
    }
    
    public function onResettingResetInitialize(GetResponseUserEvent $event)
    {
        $manager = $event->getRequest()->get('manager', null, true);
        if (isset($this->tokensTtl[$manager]) && !$event->getUser()->isPasswordRequestNonExpired($this->tokensTtl[$manager])) {
            $event->setResponse(new RedirectResponse($this->router->generate('jhv_user_resetting_request_' . $manager)));
        }
    }

    public function onResettingResetSuccess(FormEvent $event)
    {
        /** @var $user \Symfony\Component\Security\Core\User\UserInterface */
        $user = $event->getForm()->getData();

        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
    }
    
    
}