<?php

namespace JHV\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use JHV\Bundle\UserBundle\Util\UserManipulator;

/**
 * CreateUserCommand
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class CreateUserCommand extends ContainerAwareCommand
{
    
    protected function configure()
    {
        $this
            ->setName('jhv:user:create')
            ->setDescription('Create a user and persist on database.')
            ->setDefinition(array(
                new InputArgument   ('username'     , InputArgument::REQUIRED, 'Username'),
                new InputArgument   ('email'        , InputArgument::REQUIRED, 'E-mail'),
                new InputArgument   ('password'     , InputArgument::REQUIRED, 'Password'),
                
                // Identificador do manager registrado
                new InputOption     ('manager'      , null, InputOption::VALUE_REQUIRED,    'The user manager identifier'),
                new InputOption     ('super-admin'  , null, InputOption::VALUE_NONE,        'Set the user as super admin'),
                new InputOption     ('inactive'     , null, InputOption::VALUE_NONE,        'Set the user as inactive'),
            ))
            ->setHelp(<<<EOT
The <info>jhv:user:create</info> command creates a user:
<info>php app/console jhv:user:create username email password</info>

This interactive shell will ask you for some arguments in case if any argument is not written.
                    
You can define the management who will create the user:
<info>php app/console jhv:user:create username --manager="default"</info>
                    
You can also create a super admin via the super-admin flag:
<info>php app/console jhv:user:create username --super-admin</info>

You can create an inactive user (will not be able to log in):
<info>php app/console jhv:user:create username --inactive</info>

EOT
            );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username   = $input->getArgument('username');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $manager    = $input->getOption('manager');
        $inactive   = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');
        
        $handler        = $this->getContainer()->get('jhv_user.manager.handler');
        $userManager    = $handler->getUserManager($manager);
        $manipulator    = new UserManipulator($userManager);
        
        try {
            $manipulator->create($username, $email, $password, !$inactive, $superadmin);
            $output->writeln(sprintf('The user <comment>%s</comment> was successfully created.', $username));
        } catch (\Exception $exc) {
            $this->writeError($output, $exc->getMessage());
        }
    }
    
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null === $input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a username:',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('Username can not be empty');
                    }

                    return $username;
                }
            );
            $input->setArgument('username', $username);
        }

        if (null === $input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an email:',
                function($email) {
                    if (empty($email) || false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        throw new \Exception('E-mail can not be empty or is invalid');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (null === $input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a password:',
                function($password) {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }
    }
    
    protected function writeError(OutputInterface $output, $message)
    {
        $error          = <<<EOT
Error occur: 
<comment>%s</comment>
EOT;
        
        $output->writeln(sprintf($error, $message));
    }
    
}