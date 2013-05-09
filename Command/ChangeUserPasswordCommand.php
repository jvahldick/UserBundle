<?php

namespace JHV\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JHV\Bundle\UserBundle\Util\UserManipulator;

/**
 * ChangeUserPasswordCommand
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class ChangeUserPasswordCommand extends ContainerAwareCommand
{
    
    protected function configure()
    {
        $this
            ->setName('jhv:user:change-password')
            ->setDescription('Change the password of a user.')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('manager', null, InputOption::VALUE_REQUIRED, 'The "user manager" identifier, specified on configuration'),
            ))
            ->setHelp(<<<EOT
The <info>jhv:user:change-password</info> command changes the password of a user:
<info>php app/console jhv:user:change-password --manager="default" username</info>

This interactive shell will first ask you for a "user manager", after the password.

You can alternatively specify the "user manager" as option:
<info>php app/console jhv:user:change-password --manager="default" username</info>
                    
You can alternatively specify the password as a second argument:
<info>php app/console jhv:user:change-password --manager="default" username mypassword</info>

EOT
            );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username   = $input->getArgument('username');
        $password   = $input->getArgument('password');
        $manager    = $input->getOption('manager');

        $userManager = $this->getContainer()->get('jhv_user.manager.handler')->getUserManager($manager);
        $manipulator = new UserManipulator($userManager);
        $manipulator->changePassword($username, $password);

        $output->writeln(sprintf('Changed password for user <comment>%s</comment>', $username));
    }
    
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please give the username:',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('Username can not be empty');
                    }

                    return $username;
                }
            );
            $input->setArgument('username', $username);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter the new password:',
                function($password) {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }

        if (!$input->getOption('manager')) {
            $manager = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter the "user manager":',
                function($manager) {
                    if (empty($manager)) {
                        throw new \Exception('User manager can not be empty');
                    }

                    return $manager;
                }
            );
            $input->setOption('manager', $manager);
        }
    }
    
}