<?php

namespace JHV\Bundle\UserBundle\Command;

use JHV\Bundle\UserBundle\Util\UserManipulator;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * DemoteUserCommand
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class DemoteUserCommand extends RoleCommand
{
    
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('jhv:user:demote')
            ->setDescription('Demotes a user by removing a role')
            ->setHelp(<<<EOT
The <info>jhv:user:demote</info> command demotes a user by removing a role

<info>php app/console jhv:user:demote --manager="default" username ROLE_CUSTOM</info>
<info>php app/console jhv:user:demote --manager="default" --super username</info>
EOT
            );
    }
    
    protected function executeRoleCommand(OutputInterface $output, $username, $manager, $super, $role)
    {
        $userManager = $this->getContainer()->get('jhv_user.manager.handler')->getUserManager($manager);
        $manipulator = new UserManipulator($userManager);
        
        if ($super) {
            $manipulator->demote($username);
            $output->writeln(sprintf('User "%s" has been demoted as a simple user.', $username));
        } else {
            if ($manipulator->removeRole($username, $role)) {
                $output->writeln(sprintf('Role "%s" has been removed from user "%s".', $role, $username));
            } else {
                $output->writeln(sprintf('User "%s" didn\'t have "%s" role.', $username, $role));
            }
        }
    }    
    
}