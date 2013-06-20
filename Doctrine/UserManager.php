<?php

namespace JHV\Bundle\UserBundle\Doctrine;

use JHV\Bundle\UserBundle\Manager\User\UserManager as BaseUserManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;
use JHV\Bundle\UserBundle\Manager\User\Helper\UserHelperInterface;

/**
 * UserManager
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class UserManager extends BaseUserManager
{
 
    protected $class;
    protected $repository;
    protected $objectManager;
    
    public function __construct(UserHelperInterface $helper, $firewallName, ObjectManager $objectManager, $userClass)
    {
        parent::__construct($helper, $firewallName);

        $this->class            = $userClass;
        $this->objectManager    = $objectManager;
        $this->repository       = $this->objectManager->getRepository($userClass);
    }
    
    public function deleteUser(UserInterface $user)
    {
        $this->objectManager->remove($user);
        $this->objectManager->flush();
    }
    
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
    
    public function findUsers()
    {
        return $this->repository->findAll();
    }

    public function getClass()
    {
        return $this->class;
    }
    
    public function reloadUser(UserInterface $user)
    {
        $this->objectManager->refresh($user);
    }

    public function updateUser(UserInterface $user, $flush = true)
    {
        $this->getUserHelper()->updateCanonicalFields($user);
        $this->getUserHelper()->updatePassword($user);

        $this->objectManager->persist($user);
        if (true === $flush) {
            $this->objectManager->flush();
        }
    }
    
}