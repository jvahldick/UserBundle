<?php

namespace JHV\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RoleCommand
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
abstract class RoleCommand extends ContainerAwareCommand
{
    
     protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('role', InputArgument::OPTIONAL, 'Name of role'),
                new InputOption('manager', null, InputOption::VALUE_REQUIRED, 'The "user manager" identifier, specified on configuration'),
                new InputOption('super', null, InputOption::VALUE_NONE, 'Instead specifying role, use this to quickly add the super administrator role'),
            ))
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username   = $input->getArgument('username');
        $role       = $input->getArgument('role');
        $manager    = $input->getOption('manager');
        $super      = (true === $input->getOption('super'));

        if (null !== $role && $super) {
            throw new \InvalidArgumentException('You can pass either the role or the --super option (but not both simultaneously).');
        }

        if (null === $role && !$super) {
            throw new \RuntimeException('Not enough arguments.');
        }

        $this->executeRoleCommand($output, $username, $manager, $super, $role);
    }
    
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
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
        if ((true !== $input->getOption('super')) && !$input->getArgument('role')) {
            $role = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a role:',
                function($role) {
                    if (empty($role)) {
                        throw new \Exception('Role can not be empty');
                    }

                    return $role;
                }
            );
            $input->setArgument('role', $role);
        }
        if (!$input->getOption('manager')) {
            $manager = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a registered manager:',
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
    
    /**
     * @see Command
     * Método abstrado para adicionar um comando relacionado a uma regra
     * referente as permissões do usuário.
     * 
     * @param OutputInterface $output
     * @param string          $username
     * @param string          $manager
     * @param boolean         $super
     * @param string          $role
     *
     * @return void
     */
    abstract protected function executeRoleCommand(OutputInterface $output, $username, $manager, $super, $role);
    
}