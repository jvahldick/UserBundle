<?php

namespace JHV\Bundle\UserBundle\Manager\Group;

/**
 * GroupManager
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
abstract class GroupManager implements GroupManagerInterface
{
    
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
