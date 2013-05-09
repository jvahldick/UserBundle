<?php

namespace JHV\Bundle\UserBundle\Event;

use Symfony\Component\HttpFoundation\Response;

/**
 * GetResponseUserEvent
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class GetResponseUserEvent extends UserEvent
{
    private $response;

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}