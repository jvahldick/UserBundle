<?php

namespace JHV\Bundle\UserBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * LoginManager
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface LoginManagerInterface
{
    
    /**
     * Efetuar a ação de login.
     * Após criação do token este método irá acrescentar o token
     * ao security context, finalizando o login.
     * 
     * Pode ser passado uma response para execução do retorno,
     * caso contrário efetuará de acordo com o definido no firewall.
     * 
     * @param string $firewall
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function loginUser(UserInterface $user, $firewall, Response $response = null);
    
    /**
     * Efetuar a criação do token para o usuário.
     * Neste passo efetuará a ação de login do requisitante.
     * 
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param string $firewall
     */
    function createToken(UserInterface $user, $firewall);
    
}