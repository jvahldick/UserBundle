<?php

namespace JHV\Bundle\UserBundle\Manager\Group;

use JHV\Bundle\UserBundle\Model\GroupInterface;

/**
 * GroupManager
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
abstract class GroupManager implements GroupManagerInterface
{
    
    abstract public function getClass();
    abstract public function findGroupBy(array $criteria);
    abstract public function deleteGroup(GroupInterface $group);
    abstract public function findGroups();
    abstract public function updateGroup(GroupInterface $group, $flush = true);
    
    public function createGroup($name)
    {
        $class = $this->getClass();
        return new $class($name);
    }

    public function findGroupByName($name)
    {
        return $this->findGroupBy(array(
            'name' => $name
        ));
    }

    
}
