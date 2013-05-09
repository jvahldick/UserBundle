<?php

namespace JHV\Bundle\UserBundle\Manager\User\Helper;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserHelperInterface
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface UserHelperInterface
{
    
    /**
     * Canonicalizar uma string;
     * 
     * @param   string $string
     * @return  string
     */
    function canonicalize($string);
    
    /**
     * Atualizar campos canônicos.
     * Campos estes: email, username.
     * 
     * Os campos são atualizados para manter um padrão de localização,
     * evitando erros quanto a busca.
     * 
     * @param   \Symfony\Component\Security\Core\User\UserInterface $user
     * @return  void
     */
    function updateCanonicalFields(UserInterface $user);
    
    /**
     * Efetuar atualização de senha do usuário.
     * Por segurança a senha é registrada no campo plainPassword, pois
     * é a senha descrita conforme digitada pelo usuário. 
     * 
     * O método irá encriptar a senha do usuário passando-a assim para 
     * o real field password, limpando o plainPassword.
     * 
     * @param   \Symfony\Component\Security\Core\User\UserInterface $user
     * @return  void
     */
    function updatePassword(UserInterface $user);
    
}