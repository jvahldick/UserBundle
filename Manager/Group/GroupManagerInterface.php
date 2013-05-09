<?php

namespace JHV\Bundle\UserBundle\Manager\Group;

use JHV\Bundle\UserBundle\Model\GroupInterface;

/**
 * GroupManagerInterface
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface GroupManagerInterface
{
    
    function getClass();
    function findGroupBy(array $criteria);
    function deleteGroup(GroupInterface $group);
    function findGroups();
    function updateGroup(GroupInterface $group, $flush = true);
    
    /**
     * Retornará uma instancia vazia de um grupo.
     *
     * @param string $name
     * @return GroupInterface
     */
    function createGroup($name);

    /**
     * Localiza grupo através do nome.
     *
     * @return \JHV\Bundle\UserBundle\Model\GroupInterface|null
     */
    function findGroupByName($name);
    
}
