<?php

namespace JHV\Bundle\UserBundle\Manager;

use JHV\Bundle\UserBundle\Manager\User\UserManagerInterface;
use JHV\Bundle\UserBundle\Manager\Group\GroupManagerInterface;
use JHV\Bundle\UserBundle\Exception\UserManagerNotFoundException;
use JHV\Bundle\UserBundle\Exception\GroupManagerNotFoundException;

/**
 * Handler
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class Handler implements HandlerInterface
{
 
    protected $userManagers;
    protected $groupManagers;
    
    public function __construct()
    {        
        $this->userManagers = array();
        $this->groupManagers = array();
    }
    
    public function addUserManager($identifier, UserManagerInterface $userManager)
    {
        $this->userManagers[$identifier] = $userManager;
        return $this;
    }
    
    public function addGroupManager($identifier, GroupManagerInterface $groupManager)
    {
        $this->groupManagers[$identifier] = $groupManager;
        return $this;
    }

    public function getUserManager($identifier)
    {
        if (false === isset($this->userManagers[$identifier])) {
            throw new UserManagerNotFoundException(sprintf(
                'The manager "%s" cannot be found.',
                $identifier
            ));
        }
        
        return $this->userManagers[$identifier];
    }

    public function getGroupManager($identifier)
    {
        if (false === isset($this->groupManagers[$identifier])) {
            throw new GroupManagerNotFoundException(sprintf(
                'The manager "%s" cannot be found.',
                $identifier
            ));
        }
        
        return $this->groupManagers[$identifier];
    }
    
    public function getUserManagerByUserClass($class)
    {
        $manager = null;
        foreach ($this->userManagers as $userManager) {
            if ($userManager->getClass() === $class) {
                $manager = $userManager;
            }
        }
        
        if (null === $manager) {
            throw new UserManagerNotFoundException(sprintf(
                'The manager cannot be found. No manager associated with class %s',
                $class
            ));
        }
        
        return $manager;
    }
    
    public function getGroupManagerByGroupClass($class)
    {
        $manager = null;
        foreach ($this->groupManagers as $groupManager) {
            if ($groupManager->getClass() === $class) {
                $manager = $groupManager;
            }
        }
        
        if (null === $manager) {
            throw new GroupManagerNotFoundException(sprintf(
                'The manager cannot be found. No manager associated with class %s',
                $class
            ));
        }
        
        return $manager;
    }
    
}