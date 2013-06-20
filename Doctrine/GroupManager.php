<?php

namespace JHV\Bundle\UserBundle\Doctrine;

use JHV\Bundle\UserBundle\Model\GroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use JHV\Bundle\UserBundle\Manager\Group\GroupManager as BaseGroupManager;

/**
 * GroupManager
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class GroupManager extends BaseGroupManager
{
    
    protected $objectManager;
    protected $repository;
    protected $class;
    
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;        
        $this->class = $class;
        
        $this->repository = $om->getRepository($class);
    }
    
    public function getClass()
    {
        return $this->class;
    }
    
    public function deleteGroup(GroupInterface $group)
    {
        $this->objectManager->remove($group);
        $this->objectManager->flush();
    }

    public function findGroupBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    public function findGroups()
    {
        return $this->repository->findAll();
    }

    public function findGroupsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    public function updateGroup(GroupInterface $group, $flush = true)
    {
        $this->objectManager->persist($group);
        if (true === $flush)
            $this->objectManager->flush();
    }

    public function getObjectManager()
    {
        return $this->objectManager;
    }

    public function getRepository()
    {
        return $this->repository;
    }

}
