<?php

namespace JHV\Bundle\UserBundle\Util;

/**
 * UserManipulatorInterface
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface UserManipulatorInterface
{
    
    /**
     * Efetuar a criação de um usuário junto ao banco de dados.
     * 
     * @param string $username
     * @param string $email
     * @param string $password
     * @param boolean $enabled
     * @param boolean $superadmin
     * 
     * @return void
     */
    function create($username, $email, $password, $enabled, $superadmin);
    
    /**
     * Efetuar a ativação de um usuário.
     * 
     * @param string $username
     * @return void
     */
    function activate($username);
    
    /**
     * Efetuar a desativação de um usuário.
     * 
     * @param string $username
     * @return void
     */
    function deactivate($username);
    
    /**
     * Promoção de um usuário para super administrador.
     * 
     * @param string $username
     * @return void
     */
    function promote($username);
    
    /**
     * Remoção da regra de usuário para super administrador.
     * 
     * @param string $username
     * @return void
     */
    function demote($username);
    
    /**
     * Adicionar uma nova regra ao usuário.
     * 
     * @param string $username
     * @param string $role
     * 
     * @return boolean TRUE|FALSE True caso a regra seja adicionada com sucesso
     */
    function addRole($username, $role);
    
    /**
     * Remover uma regra do usuário.
     * 
     * @param string $username
     * @param string $role
     * 
     * @return boolean TRUE|FALSE True caso a regra seja removida com sucesso
     */
    function removeRole($username, $role);
    
    /**
     * Efetuar alteração de senha do usuário.
     * 
     * @param string $username
     * @param string $password
     */
    function changePassword($username, $password);
    
}