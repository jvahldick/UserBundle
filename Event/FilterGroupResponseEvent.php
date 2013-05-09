<?php

namespace JHV\Bundle\UserBundle\Event;

use JHV\Bundle\UserBundle\Model\GroupInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * FilterGroupResponseEvent
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class FilterGroupResponseEvent extends GroupEvent
{
	
	protected $response;

	public function __construct(GroupInterface $group, Request $request, Response $response)
	{
		parent::__construct($group, $request);
		$this->response = $response;
	}

	public function setResponse(Response $response)
	{
		$this->response = $response;
	}

	public function getResponse()
	{
		return $this->response;
	}
	
}