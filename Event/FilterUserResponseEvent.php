<?php

namespace JHV\Bundle\UserBundle\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * FilterUserResponseEvent
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class FilterUserResponseEvent extends UserEvent
{
    
    protected $response;

    public function __construct(UserInterface $user, Request $request, $firewallName, Response $response)
    {
        parent::__construct($user, $request, $firewallName);
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    
}