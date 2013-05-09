<?php

namespace JHV\Bundle\UserBundle\Model;

/**
 * UserRoleInterface
 *
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @copyright (c) 2013, Jorge Vahldick
 * @license MIT on Resources/meta/LICENSE
 */
interface UserRoleInterface
{
    
    const ROLE_DEFAULT      = 'ROLE_USER';
    const ROLE_SUPER_ADMIN  = 'ROLE_SUPER_ADMIN';
    
    /**
     * Verificará se o usuário possui regras de super adminsitrador.
     * 
     * @return boolean
     */
    function isSuperAdmin();
    
    /**
     * Definir o usuário como super administrador.
     * 
     * @param boolean $boolean
     */
    function setSuperAdmin($boolean);
    
    /**
     * Adicionar uma regra ao usuário.
     * 
     * @param string $role
     * @return self
     */
    function addRole($role);
    
    /**
     * Verificar se o usuário possui uma regra específica.
     * 
     * @param string $role
     * @return boolean
     */
    function hasRole($role);
    
    /**
     * Remover alguma regra do usuário.
     * 
     * @param string $role
     * @return self
     */
    function removeRole($role);
    
    /**
     * Definir regras ao usuário.
     * 
     * @param array $roles
     * @return self
     */
    function setRoles($roles);
    
}