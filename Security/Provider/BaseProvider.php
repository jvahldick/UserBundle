<?php

namespace JHV\Bundle\UserBundle\Security\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use JHV\Bundle\UserBundle\Manager\User\UserManagerInterface;

/**
 * BaseProvider
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
abstract class BaseProvider implements BaseProviderInterface
{
    
    protected $userManager;
    
    public function __construct(UserManagerInterface $manager)
    {
        $this->userManager = $manager;
    }
    
    public function loadUserByUsername($username)
    {
        $user = $this->findUser($username);
        if (null === $user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
        
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $providerUserClass = $this->userManager->getClass();
        if (false === $user instanceof $providerUserClass) {
            throw new UnsupportedUserException(sprintf('Usuário "%s" não compatível com provedor de acesso', $user->getUsername()));
        }

        if (null === $reloadedUser = $this->userManager->findUserBy(array('id' => $user->getId()))) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    public function supportsClass($class)
    {
        $userClass = $this->userManager->getClass();
        return $userClass === $class || is_subclass_of($class, $userClass);
    }
    
}