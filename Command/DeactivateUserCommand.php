<?php

namespace JHV\Bundle\UserBundle\Command;

use JHV\Bundle\UserBundle\Util\UserManipulator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * DeactivateUserCommand
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class DeactivateUserCommand extends ActivationCommand
{
    
    protected function configure()
    {
        $this
            ->setName('jhv:user:deactivate')
            ->setDescription('Deactivate a user')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputOption('manager', null, InputOption::VALUE_REQUIRED, 'The "user manager" identifier, specified on configuration'),
            ))
            ->setHelp(<<<EOT
The <infojhvfos:user:deactivate</info> command deactivates a user (so they will not be able to log in):
<info>php app/console jhv:user:deactivate username --manager="default"</info>
EOT
            );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $manager = $input->getOption('manager');

        $userManager = $this->getContainer()->get('jhv_user.manager.handler')->getUserManager($manager);
        $manipulator = new UserManipulator($userManager);
        $manipulator->deactivate($username);

        $output->writeln(sprintf('User "%s" has been deactivated.', $username));
    }

}
