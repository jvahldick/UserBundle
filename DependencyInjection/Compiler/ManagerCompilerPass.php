<?php

namespace JHV\Bundle\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * ManagerCompilerPass
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class ManagerCompilerPass implements CompilerPassInterface
{
    
    public function process(ContainerBuilder $container)
    {
        $this->processUserManagers($container);
        $this->processGroupManagers($container);
    }
    
    /**
     * Processamento dos gerenciadores de usuário.
     * O intuito do método é localizar todas as definições taxadas com a tag
     * de "jhv_user.user_manager" adicionando-as no manuseador de gerenciadores.
     * 
     * O objetivo do "Handler" é controlar os gerenciadores registrados, não
     * havendo assim a necessidade de efetuar duplicação de itens que contem
     * algum ObjectManager.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function processUserManagers(ContainerBuilder $container)
    {
        if (true === $container->hasDefinition('jhv_user.manager.handler')) {
            $definition = $container->getDefinition('jhv_user.manager.handler');
            
            foreach ($container->findTaggedServiceIds('jhv_user.user_manager') as $id => $attributes) {
                $definition->addMethodCall('addUserManager', array($attributes[0]['identifier'], new Reference($id)));
            }
        }
    }
    
    /**
     * Processamento de gerenciadores de grupos.
     * Os grupos armazenam regras para usuário.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function processGroupManagers(ContainerBuilder $container)
    {
        if (true === $container->hasDefinition('jhv_user.manager.handler')) {
            $definition = $container->getDefinition('jhv_user.manager.handler');
            
            foreach ($container->findTaggedServiceIds('jhv_user.group_manager') as $id => $attributes) {
                $definition->addMethodCall('addGroupManager', array($attributes[0]['identifier'], new Reference($id)));
            }
        }
    }
       
}