<?php

namespace JHV\Bundle\UserBundle\Mailer;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * MailerInterface
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface MailerInterface
{
    
    /**
     * Efetua a preparação e envio da mensagem para resetar a senha da conta.
     * 
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param sring $manager Gerenciador em questão
     * @return void
     */
    function sendResettingEmailMessage(UserInterface $user, $manager);
    
}