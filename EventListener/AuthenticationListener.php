<?php

namespace JHV\Bundle\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use JHV\Bundle\UserBundle\JHVUserEvents;
use JHV\Bundle\UserBundle\Security\LoginManagerInterface;
use JHV\Bundle\UserBundle\Event\FilterUserResponseEvent;
use JHV\Bundle\UserBundle\Event\UserEvent;

/**
 * AuthenticationListener
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class AuthenticationListener implements EventSubscriberInterface
{
    
    protected $loginManager;
    
    public function __construct(LoginManagerInterface $loginManager)
    {
        $this->loginManager = $loginManager;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            JHVUserEvents::REGISTRATION_COMPLETED => 'authenticate',
            JHVUserEvents::REGISTRATION_CONFIRMED => 'authenticate',
            JHVUserEvents::RESETTING_RESET_COMPLETED => 'authenticate',
        );
    }
    
    public function authenticate(FilterUserResponseEvent $event)
    {
        if (true === $event->getUser()->isEnabled()) {
            try {
                $this->loginManager->loginUser($event->getUser(), $event->getFirewallName(), $event->getResponse());
                $event->getDispatcher()->dispatch(JHVUserEvents::SECURITY_IMPLICIT_LOGIN, new UserEvent($event->getUser(), $event->getRequest(), $event->getFirewallName()));
            } catch (AccountStatusException $ex) {
            }
        }
    }
    
}