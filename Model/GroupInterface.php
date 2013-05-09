<?php

namespace JHV\Bundle\UserBundle\Model;

/**
 * GroupInterface
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface GroupInterface
{

    /**
     * @param string $role
     *
     * @return self
     */
    function addRole($role);

    /**
     * @return string
     */
    function getName();

    /**
     * @param string $role
     *
     * @return boolean
     */
    function hasRole($role);

    /**
     * @return array
     */
    function getRoles();

    /**
     * @param string $role
     *
     * @return self
     */
    function removeRole($role);

    /**
     * @param string $name
     *
     * @return self
     */
    function setName($name);

    /**
     * @param array $roles
     *
     * @return self
     */
    function setRoles(array $roles);
}
