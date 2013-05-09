<?php

namespace JHV\Bundle\UserBundle\Command;

use JHV\Bundle\UserBundle\Util\UserManipulator;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * PromoteUserCommand
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class PromoteUserCommand extends RoleCommand
{
    
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('jhv:user:promote')
            ->setDescription('Promotes a user by adding a role')
            ->setHelp(<<<EOT
The <info>jhv:user:promote</info> command promotes a user by adding a role

<info>php app/console jhv:user:promote --manager="default" username ROLE_CUSTOM</info>
<info>php app/console jhv:user:promote --manager="default" --super username</info>
EOT
            );
    }
    
    protected function executeRoleCommand(OutputInterface $output, $username, $manager, $super, $role)
    {
        $userManager = $this->getContainer()->get('jhv_user.manager.handler')->getUserManager($manager);
        $manipulator = new UserManipulator($userManager);
        
        if (true === $super) {
            $manipulator->promote($username);
            $output->writeln(sprintf('User "%s" has been promoted as a super administrator.', $username));
        } else {
            if ($manipulator->addRole($username, $role)) {
                $output->writeln(sprintf('Role "%s" has been added to user "%s".', $role, $username));
            } else {
                $output->writeln(sprintf('User "%s" did already have "%s" role.', $username, $role));
            }
        }
    }    
    
}