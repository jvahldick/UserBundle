<?php

namespace JHV\Bundle\UserBundle\Security\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * AuthenticationProvider
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface BaseProviderInterface extends UserProviderInterface
{
    
    /**
     * Efetuar a busca do usuário.
     * 
     * @param string $string
     * @return UserInterface|null
     */
    function findUser($string);
    
}