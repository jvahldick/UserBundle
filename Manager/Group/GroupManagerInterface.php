<?php

namespace JHV\Bundle\UserBundle\Manager\Group;

use Doctrine\ORM\EntityRepository;
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

    /**
     * Verificar o nome da classe de grupos definida.
     *
     * @return string
     */
    function getClass();
    function findGroupBy(array $criteria);
    function deleteGroup(GroupInterface $group);
    function findGroups();
    function findGroupsBy(array $criteria);
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
     * @param string $name
     * @return \JHV\Bundle\UserBundle\Model\GroupInterface|null
     */
    function findGroupByName($name);
    
}
