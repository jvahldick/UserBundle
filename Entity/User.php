<?php

namespace JHV\Bundle\UserBundle\Entity;

use JHV\Bundle\UserBundle\Model\User as BaseUser;

/**
 * User
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class User extends BaseUser
{
    
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
    
}