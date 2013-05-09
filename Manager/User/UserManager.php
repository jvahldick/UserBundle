<?php

namespace JHV\Bundle\UserBundle\Manager\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use JHV\Bundle\UserBundle\Manager\User\Helper\UserHelperInterface;

/**
 * UserManager
 *
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @copyright (c) 2013, Jorge Vahldick
 * @license MIT on Resources/meta/LICENSE
 */
abstract class UserManager implements UserManagerInterface
{
    
    protected $firewallName;
    protected $userHelper;
    
    public function __construct(UserHelperInterface $helper, $firewallName)
    {
        $this->userHelper = $helper;
        $this->firewallName = $firewallName;
    }
    
    abstract function deleteUser(UserInterface $user);
    abstract function findUserBy(array $criteria);
    abstract function findUsers();
    abstract function getClass();
    abstract function reloadUser(UserInterface $user);
    abstract function updateUser(UserInterface $user, $flush = true);
    
    public function createUser()
    {
        $class = $this->getClass();
        return new $class;
    }

    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(array('confirmationToken' => $token));
    }
    
    public function findUserByEmail($email)
    {
        return $this->findUserBy(array('emailCanonical' => $this->getUserHelper()->canonicalize($email)));
    }

    public function findUserByUsername($username)
    {
        return $this->findUserBy(array('usernameCanonical' => $this->getUserHelper()->canonicalize($username)));
    }

    public function findUserByUsernameOrEmail($string)
    {
        return (false !== filter_var($string, FILTER_VALIDATE_EMAIL)) 
            ? $this->findUserByEmail($string) 
            : $this->findUserByUsername($string)
        ;
    }
    
    public function refreshUser(UserInterface $user)
    {
        $class = $this->getClass();
        if (false === $user instanceof $class) {
            throw new UnsupportedUserException('Account instance is invalid.');
        }
        
        $refreshedUser = $this->findUserBy(array('id' => $user->getId()));
        if (null === $refreshedUser) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }
        
        return $refreshedUser;
    }
    
    public function getFirewallName()
    {
        return $this->firewallName;
    }
    
    public function getUserHelper()
    {
        return $this->userHelper;
    }
    
}