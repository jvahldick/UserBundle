<?php

namespace JHV\Bundle\UserBundle\Manager\User;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserManagerInterface
 *
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @copyright (c) 2013, Jorge Vahldick
 * @license MIT on Resources/meta/LICENSE
 */
interface UserManagerInterface
{
    
    /**
     * Efetua a criação do usuário através da classe definida por DI.
     * 
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    function createUser();
    
    /**
     * Localizar usuário através de um token.
     * O token será gravado caso usuário efetue uma requisição de senha.
     * 
     * @param string $token
     * @return \Symfony\Component\Security\Core\User\UserInterface|null
     */
    function findUserByConfirmationToken($token);
    
    /**
     * Procurar usuário por e-mail
     * 
     * @param string $email
     * @return \Symfony\Component\Security\Core\User\UserInterface|null
     */
    function findUserByEmail($email);
    
    /**
     * Procurar usuário por nome.
     * 
     * @param string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface|null
     */
    function findUserByUsername($username);
    
    /**
     * Tentar efetuar a localização do usuário por nome de usuário ou e-mail.
     * 
     * @param string $string
     * @return \Symfony\Component\Security\Core\User\UserInterface|null
     */
    function findUserByUsernameOrEmail($string);
    
    /**
     * Efetua a atualização do usuário.
     * Localizará o usuário do banco de dados para atualização de informações.
     * 
     * @param   \Symfony\Component\Security\Core\User\UserInterface $user
     * @return  \Symfony\Component\Security\Core\User\UserInterface
     */
    function refreshUser(UserInterface $user);
    
    /**
     * Localizar o helper do usuário.
     * 
     * @return \JHV\Bundle\UserBundle\Manager\Helper\UserHelperInterface
     */
    function getUserHelper();
    
    /**
     * Localizar o nome do firewall associado com o manager em questão
     * 
     * @return string
     */
    function getFirewallName();
    
}
