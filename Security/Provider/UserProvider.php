<?php

namespace JHV\Bundle\UserBundle\Security\Provider;

/**
 * UserProvider
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class UserProvider extends BaseProvider
{
    
    public function findUser($string)
    {
        return $this->userManager->findUserByUsername($string);
    }
    
}