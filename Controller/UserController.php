<?php

namespace JHV\Bundle\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * UserController
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
abstract class UserController extends ContainerAware
{
    
    /**
     * Efetuar a localização do container de execução da chamada.
     * 
     * @return \Symfony\Component\DependencyInjection\ContainerAware
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Localizar o serviço de despacho de eventos.
     * 
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    /**
     * Localizar o serviço de construção de formulários.
     * 
     * @return \Symfony\Component\Form\FormFactory
     */
    protected function getFormFactory()
    {
        return $this->container->get('form.factory');
    }
    
    /**
     * Localizar o manipulador dos gerenciadores.
     * 
     * @return \JHV\Bundle\UserBundle\Manager\HandlerInterface
     */
    protected function getManagerHandler()
    {
        return $this->container->get('jhv_user.manager.handler');
    }
    
    /**
     * Localização do gerenciador de usuários.
     * 
     * @param string $manager
     * @return \JHV\Bundle\UserBundle\Manager\User\UserManagerInterface
     */
    protected function getUserManager($manager)
    {
        return $this->getManagerHandler()->getUserManager($manager);
    }
    
    /**
     * Localização do gerenciador de grupos.
     * 
     * @param string $manager
     * @return \JHV\Bundle\UserBundle\Manager\Group\GroupManagerInterface
     */
    protected function getGroupManager($manager)
    {
        return $this->getManagerHandler()->getGroupManager($manager);
    }
    
    /**
     * Buscar o serviço de renderização de templates.
     * 
     * @return \JHV\Bundle\UserBundle\Template\RendererInterface
     */
    protected function getTemplateRenderer()
    {
        $manager = $this->container->get('request')->get('manager');
        if (empty($manager)) {
            throw new \RuntimeException('Problem to get template renderer, manager was not defined.');
        }
        
        return $this->container->get(sprintf('jhv_user.template.%s_renderer', $this->container->get('request')->get('manager')));
    }
    
    
    /**
     * Localizar o template engine para disposição dos templates.
     * 
     * @return \Symfony\Bundle\TwigBundle\TwigEngine
     */
    protected function getTemplating()
    {
        return $this->container->get('templating');
    }
    
    /**
     * Localiza o usuário autenticado no sistema.
     * Caso não haja usuário autenticado ou haja algum problema nas propriedades
     * do usuário, ocorrerá um erro.
     * 
     * @return  \Symfony\Component\Security\Core\User\UserInterface
     * @throws  \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function getAuthenticatedUser()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        // Verifica se o usuário realmente existe
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        // Verifica validade do usuário através do security checker
        $this->container->get('security.user_checker')->checkPostAuth($user);
        
        return $user;
    }

}