<?php

namespace JHV\Bundle\UserBundle\Model;

/**
 * GroupableInterface
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface GroupableInterface
{

    /**
     * Localizar os grupos associados ao usuário.
     *
     * @return \Traversable
     */
    function getGroups();

    /**
     * Localizar um array com nome de todos os nomes dos grupos.
     * 
     * @return array
     */
    function getGroupNames();

    /**
     * Verificar a existência de um grupo através do nome
     *
     * @param string $name Nome do grupo
     * @return Boolean
     */
    function hasGroup($name);

    /**
     * Adicionar um grupo na listagem de grupos do usuário.
     *
     * @param GroupInterface $group
     * @return self
     */
    function addGroup(GroupInterface $group);

    /**
     * Remover um grupo da listagem de grupos do usuário.
     *
     * @param GroupInterface $group
     * @return self
     */
    function removeGroup(GroupInterface $group);
    
}
