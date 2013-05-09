<?php

namespace JHV\Bundle\UserBundle\Model;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use \Serializable as Serializable;

/**
 * UserInterface
 *
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @copyright (c) 2013, Jorge Vahldick
 * @license MIT on Resources/meta/LICENSE
 */
interface UserInterface extends AdvancedUserInterface, EquatableInterface, UserRoleInterface, Serializable
{
    
    /**
     * Limpar o campo plainPassword
     * @return void
     */
    function eraseCredentials();
    
    /**
     * Define se o usuário está expirado.
     * 
     * @param boolean $boolean
     */
    function setAccountExpired($boolean);
    
    /**
     * Define quando a conta será expirada.
     * 
     * @param \DateTime $expirationDate
     * @return self
     */
    function setAccountExpiresAt(\DateTime $expirationDate);
    
    /**
     * Define um token para recuperação de senha.
     * 
     * @param string $token
     * @return self
     */
    function setConfirmationToken($token);
    
    /**
     * Definição de quando a senha do usuário será expirada.
     * 
     * @param \DateTime $expirationDate
     * @return self
     */
    function setCredentialsExpiresAt(\DateTime $expirationDate);
    
    /**
     * Definir um e-mail para o usuário.
     * 
     * @param string $email
     * @return self
     */
    function setEmail($email);
    
    /**
     * Definir e-mail do usuário de uma forma tratada.
     * 
     * @param string $email
     * @return self
     */
    function setEmailCanonical($email);
    
    /**
     * Define se o usuário está habilitado para acesso.
     * 
     * @param boolean $boolean
     * @return self
     */
    function setEnabled($boolean);
    
    /**
     * Define se o usuário está trancado para acesso.
     * 
     * @param boolean $boolean
     * @return self
     */
    function setLocked($boolean);
    
    /**
     * Definir senha em formato texto.
     * Por questões de segurança é importante que seja
     * definido este formato de senha, e não diretamente
     * o password.
     * 
     * @param string $password
     * @return self
     */
    function setPlainPassword($password);
    
    /**
     * Definição da senha do usuário.
     * Esta senha deverá estar encriptografada, conforme
     * será salvo no banco de dados.
     * 
     * @param type $password
     */
    function setPassword($password);
    
    /**
     * Definir usuário.
     * 
     * @param string $username
     * @return self
     */
    function setUsername($username);
    
    /**
     * Definir nome do usuário tratado.
     * 
     * @param string $username
     * @return self
     */
    function setUsernameCanonical($username);
    
    /**
     * Definir última data de login do cliente.
     * 
     * @param \DateTime $datetime
     * @return self
     */
    function setLastLoginAt(\DateTime $datetime);
    
    /**
     * Localizar data do último login efetuado pelo usuário.
     * 
     * @return \DateTime
     */
    function getLastLoginAt();
    
    /**
     * Definir requisição para recuperação de senha.
     * 
     * @param \DateTime $datetime
     * @return self
     */
    function setPasswordRequestedAt(\DateTime $datetime = null);
    
    /**
     * Verifica se o usuário está definido como expirado.
     * Chamada inversa para o método isAccountNonExpired
     * 
     * @return boolean
     */
    function isAccountExpired();
    
    /**
     * Verifica se a senha do usuário está expirada.
     * Chamada inversa ao método isCredentialsNonExpired
     * 
     * @return boolean
     */
    function isCredentialsExpired();
    
    /**
     * Verifica se o usuário está habilitado.
     * 
     * @return boolean
     */
    function isEnabled();
    
    /**
     * Verifica se o usuário está bloqueado para acesso.
     * 
     * @return boolean
     */
    function isLocked();
    
    /**
     * Verifica se o usuário é válido.
     * Álias para chamada de equals
     * 
     * @param \JHV\Bundle\UserBundle\Model\UserInterface $user
     * @return self
     */
    function isUser(UserInterface $user = null);
    
    /**
     * O período de requisição da senha está expirado?
     * Verifica se a requisição para senha está expirada.
     * 
     * @param integer $timeToLive
     * @return boolean
     */
    function isPasswordRequestNonExpired($timeToLive);
    
    /**
     * Verifica a data no qual a conta será expirada.
     * 
     * @return \DateTime|null
     */
    function getAccountExpiresAt();
    
    /**
     * Localiza o token de confirmação para recuperação de senha.
     * 
     * @return string
     */
    function getConfirmationToken();
    
    /**
     * Verifica a data de validação da senha
     * 
     * @return \DateTime|null
     */
    function getCredentialsExpiresAt();
    
    /**
     * Localizar o e-mail definido ao usuário
     * 
     * @return string
     */
    function getEmail();
    
    /**
     * Localiza senha definida.
     * 
     * @return string
     */
    function getPlainPassword();
    
}