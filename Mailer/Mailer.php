<?php

namespace JHV\Bundle\UserBundle\Mailer;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Mailer
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class Mailer implements MailerInterface
{
    
    protected $router;
    protected $mailer;
    protected $twig;
    protected $parameters;
    protected $emails;
    
    public function __construct($mailer, RouterInterface $router, \Twig_Environment $twig, array $parameters, array $emailsConfig)
    {
        $this->mailer       = $mailer;
        $this->router       = $router;
        $this->twig         = $twig;
        $this->parameters   = $parameters;
        $this->emails       = $emailsConfig;
    }
    
    public function sendResettingEmailMessage(UserInterface $user, $manager)
    {
        $template = $this->parameters[$manager]['files']['resetting_email'];
        $goTo = $this->router->generate('jhv_user_resetting_reset_' . $manager, array('token' => $user->getConfirmationToken()), true);
        $context = array(
            'user' => $user,
            'confirmationUrl' => $goTo
        );
        
        $this->sendMessage(
            $template, 
            $context,
            $this->emails[$manager]['resetting']['from_sender'],
            $this->emails[$manager]['resetting']['from_address'], 
            $user->getEmail()
        );
    }
    
    /**
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendMessage($templateName, $context, $fromName, $fromEmail, $toEmail)
    {
        $template   = $this->twig->loadTemplate($templateName);
        $subject    = $template->renderBlock('subject', $context);
        $textBody   = $template->renderBlock('body_text', $context);
        $htmlBody   = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail, $fromName)
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message
                ->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain')
            ;
        } else {
            $message->setBody($textBody);
        }

        $this->mailer->send($message);
    }
    
}