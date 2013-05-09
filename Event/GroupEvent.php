<?php

namespace JHV\Bundle\UserBundle\Event;

use JHV\Bundle\UserBundle\Model\GroupInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * GroupEvent
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class GroupEvent extends Event
{
    
    protected $group;
    protected $request;
    
    public function __construct(GroupInterface $group, Request $request)
    {
        $this->group = $group;
        $this->request = $request;
    }
    
    public function getGroup()
    {
        return $this->group;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
}
