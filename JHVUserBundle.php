<?php

namespace JHV\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use JHV\Bundle\UserBundle\DependencyInjection\Compiler\ManagerCompilerPass;
use JHV\Bundle\UserBundle\DependencyInjection\Compiler\RouterCompilerPass;

class JHVUserBundle extends Bundle
{
    
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ManagerCompilerPass());
    }
    
}
