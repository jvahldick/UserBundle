<?php

namespace JHV\Bundle\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserEvent
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class UserEvent extends Event
{
        
    protected $user;
    protected $request;
    protected $firewallName;

    public function __construct(UserInterface $user, Request $request, $firewallName)
    {
        if (!$request->get('manager', null, true)) {
            throw new \RuntimeException('Manager cannot be found to event.');
        }
        
        $this->user = $user;
        $this->request = $request;
        $this->firewallName = $firewallName;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getFirewallName()
    {
        return $this->firewallName;
    }
    
    /**
     * @return string
     */
    public function getManagerName()
    {
        return $this->managerName;
    }
    
}