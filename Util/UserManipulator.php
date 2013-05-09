<?php

namespace JHV\Bundle\UserBundle\Util;

use JHV\Bundle\UserBundle\Manager\User\UserManagerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * UserManipulator
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class UserManipulator implements UserManipulatorInterface
{
    
    protected $userManager;
    
    /**
     * Construtor.
     * Definição da gerenciador de entidades para contemplar
     * as modificações realizadas referente ao usuário.
     * 
     * @param \JHV\Bundle\UserBundle\Manager\User\UserManagerInterface $manager
     */
    public function __construct(UserManagerInterface $manager)
    {
        $this->userManager = $manager;
    }
    
    public function create($username, $email, $password, $enabled, $superadmin)
    {
        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled($enabled);
        $user->setSuperAdmin($superadmin);
        
        $this->userManager->updateUser($user);
    }
    
    public function changePassword($username, $password)
    {
        $user = $this->findUserByUsername($username);
        $user->setPlainPassword($password);
        
        $this->userManager->updateUser($user);
    }
    
    public function activate($username)
    {
        $user = $this->findUserByUsername($username);
        $user->setEnabled(true);
        
        $this->userManager->updateUser($user);
    }

    public function deactivate($username)
    {
        $user = $this->findUserByUsername($username);
        $user->setEnabled(false);
        
        $this->userManager->updateUser($user);
    }
    
    public function demote($username)
    {
        $this->changeAdminRole($username, false);
    }
    
    public function promote($username)
    {
        $this->changeAdminRole($username, true);
    }
    
    public function addRole($username, $role)
    {
        $user = $this->findUserByUsername($username);
        if (true === $user->hasRole($role)) {
            return false;
        }
        
        $user->addRole($role);
        $this->userManager->updateUser($user);

        return true;
    }
    
    public function removeRole($username, $role)
    {
        $user = $this->findUserByUsername($username);
        if (false === $user->hasRole($role)) {
            return false;
        }
        
        $user->removeRole($role);
        $this->userManager->updateUser($user);

        return true;
    }
    
    /**
     * Alterar regra de super-admin para o usuário.
     * 
     * @param string $username
     * @param boolean $isSuperAdmin
     */
    protected function changeAdminRole($username, $isSuperAdmin)
    {
        $user = $this->findUserByUsername($username);
        $user->setSuperAdmin($isSuperAdmin);
        $this->userManager->updateUser($user);
    }
    
    /**
     * Método auxiliar para localizar o usuário pelo username.
     * 
     * @param string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws UsernameNotFoundException Caso o usuário não exista
     */
    protected function findUserByUsername($username)
    {
        $user = $this->userManager->findUserByUsername($username);
        if (null === $user) {
            throw new UsernameNotFoundException(sprintf('User identified by "%s" username does not exist.', $username));
        }
        
        return $user;
    }
    
}