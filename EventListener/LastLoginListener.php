<?php

namespace JHV\Bundle\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use JHV\Bundle\UserBundle\Manager\HandlerInterface;
use JHV\Bundle\UserBundle\Event\UserEvent;
use JHV\Bundle\UserBundle\JHVUserEvents;

/**
 * LastLoginListener
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class LastLoginListener implements EventSubscriberInterface
{
    
    protected $userManagerHandler;
    
    public function __construct(HandlerInterface $handler)
    {
        $this->userManagerHandler = $handler;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            JHVUserEvents::SECURITY_IMPLICIT_LOGIN  => 'onImplicityLogin',
            SecurityEvents::INTERACTIVE_LOGIN       => 'onSecurityInteractiveLogin',
        );
    }
    
    public function onImplicityLogin(UserEvent $event)
    {
        $user = $event->getUser();
        $class = get_class($user);

        $user->setLastLoginAt(new \DateTime());
        $this->userManagerHandler->getUserManagerByUserClass($class)->updateUser($user);
    }
    
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();  

        if ($user instanceof UserInterface) {
            $user->setLastLoginAt(new \DateTime());
            
            $class = get_class($user);
            $this->userManagerHandler->getUserManagerByUserClass($class)->updateUser($user);
        }
    }

}