<?php

namespace JHV\Bundle\UserBundle\Entity;

use JHV\Bundle\UserBundle\Model\Group as BaseGroup;

/**
 * Group
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class Group extends BaseGroup
{
    
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
    
}