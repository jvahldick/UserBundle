<?php

namespace JHV\Bundle\UserBundle\Security\Provider;

/**
 * EmailProvider
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class EmailProvider extends BaseProvider
{
    
    public function findUser($string)
    {
        return $this->userManager->findUserByUsernameOrEmail($string);
    }
    
}