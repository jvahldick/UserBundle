<?php

namespace JHV\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ActivationCommand
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
abstract class ActivationCommand extends ContainerAwareCommand
{
    
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
        
        if (!$input->getOption('manager')) {
            $manager = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a manager:',
                function($manager) {
                    if (empty($manager)) {
                        throw new \Exception('Manager can not be empty');
                    }

                    return $manager;
                }
            );
            $input->setOption('manager', $manager);
        }
    }
    
}