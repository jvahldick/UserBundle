<?php

namespace JHV\Bundle\UserBundle\Manager;

use JHV\Bundle\UserBundle\Manager\User\UserManagerInterface;
use JHV\Bundle\UserBundle\Manager\Group\GroupManagerInterface;

/**
 * HandlerInterface
 * Manipular os gerenciadores de usuários e grupos.
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface HandlerInterface
{
    
    /**
     * Adicionar gerenciador de usuários.
     * Método com intuito de armazenas em um array os gerenciadores
     * de usuários disponíveis. Neste será armazenado um identificador
     * dos gerenciadores junto a seu dispositivo de conexão.
     * 
     * @param   string $identifier
     * @param   \JHV\Bundle\UserBundle\Manager\User\UserManagerInterface $userManager
     * @return  self
     */
    function addUserManager($identifier, UserManagerInterface $userManager);
    
    /**
     * Localizará um gerenciador de usuários através do seu nome identificador,
     * caso o mesmo não exista será retornado um erro.
     * 
     * @return \JHV\Bundle\UserBundle\Manager\User\UserManagerInterface
     * @throws \JHV\Bundle\UserBundle\Exception\UserManagerNotFoundException
     */
    function getUserManager($identifier);
    
    /**
     * Localizar o gerenciador de usuário pela classe de usuário.
     * Este método irá percorrer os gerenciadores registrados verificando
     * a compatibilidade da classe enviada por parâmetro junto as as classes
     * associadas aos gerenciadores.
     * 
     * @param string $class
     * @return \JHV\Bundle\UserBundle\Manager\User\UserManagerInterface
     * @throws \JHV\Bundle\UserBundle\Exception\UserManagerNotFoundException
     */
    function getUserManagerByUserClass($class);
    
    /**
     * Adicionar gerenciador de grupos.
     * Método irá acrescentar a um array o objeto de gerenciamento de grupos,
     * trabalhando o grupo / banco de dados.
     * 
     * @param type $identifier
     * @param \JHV\Bundle\UserBundle\Manager\Group\GroupManagerInterface $groupManager
     * @return self
     */
    function addGroupManager($identifier, GroupManagerInterface $groupManager);
    
    /**
     * Localizará um gerenciador de grupos de regras através de seu nome
     * identificador, não existindo causará erro.
     * 
     * @param string $identifier
     * @return \JHV\Bundle\UserBundle\Manager\Group\GroupManagerInterface
     * @throws \JHV\Bundle\UserBundle\Exception\GroupManagerNotFoundException
     */
    function getGroupManager($identifier);
    
    /**
     * Localizar o gerenciador de grupos através de uma classe.
     * O método irá percorrer os gerenciadores de grupo até achar o adequado.
     * 
     * Caso o gerenciador não seja encontrado, será gerado um erro.
     * 
     * @param string $class
     * @return \JHV\Bundle\UserBundle\Manager\Group\GroupManagerInterface
     * @throws \JHV\Bundle\UserBundle\Exception\GroupManagerNotFoundException
     */
    function getGroupManagerByGroupClass($class);
    
}