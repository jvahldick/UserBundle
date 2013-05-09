<?php

namespace JHV\Bundle\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JHV\Bundle\UserBundle\JHVUserEvents;
use JHV\Bundle\UserBundle\Event\FormEvent;
use JHV\Bundle\UserBundle\Event\GroupEvent;
use JHV\Bundle\UserBundle\Event\FilterGroupResponseEvent;
use JHV\Bundle\UserBundle\Event\GetResponseGroupEvent;

/**
 * GroupController
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class GroupController extends UserController
{
    
    /**
     * Efetuar a localização dos grupos
     * Listagem dos grupos.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $manager    = $request->get('manager');
        $groups     = $this->getGroupManager($manager)->findGroups();
        
        return $this->getTemplateRenderer()->renderResponse('group_list', array(
            'groups' => $groups
        ));
    }
    
    /**
     * Efetuar a criação de um novo grupo.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $manager            = $request->get('manager');
        $groupManager       = $this->getGroupManager($manager);
        $eventDispatcher    = $this->getEventDispatcher();
        
        $group = $groupManager->createGroup('');
        $eventDispatcher->dispatch(JHVUserEvents::GROUP_CREATE_INITIALIZE, new GroupEvent($group, $request));
        
        // Criação do formulário
        $form = $this->getFormFactory()->create('jhv_user_group_type', $group, array(
            'data_class' => get_class($group)
        ));
        
        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $eventDispatcher->dispatch(JHVUserEvents::GROUP_CREATE_SUCCESS, $event);

                $groupManager->updateGroup($group);
                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('jhv_user_groups_list_' . $manager);
                    $response = new RedirectResponse($url);
                }

                $eventDispatcher->dispatch(JHVUserEvents::GROUP_CREATE_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));
                return $response;
            }
        }
        
        return $this->getTemplateRenderer()->renderResponse('group_create', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * Efetuar a edição de um grupo.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $groupId
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $groupId)
    {
        $eventDispatcher = $this->getEventDispatcher();

        $manager        = $request->get('manager');
        $groupManager   = $this->getGroupManager($manager);
        $group          = $groupManager->findGroupBy(array(
            'id' => $groupId
        ));

        // Caso o grupo não tenha sido encontrado, irá gerar erro
        if (!$group) {
            throw new NotFoundHttpException(sprintf(
                'The group %s cannot be found.',
                $groupId
            ));
        }

        // Chamada de inicialização do evento
        $event = new GetResponseGroupEvent($group, $request);
        $eventDispatcher->dispatch(JHVUserEvents::GROUP_EDIT_INITIALIZE, $event);

        // Caso a resposta não seja nula
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        // Criação do formulário para edição
        $form = $this->getFormFactory()->create('jhv_user_group_type', $group, array(
            'data_class' => get_class($group)
        ));

        // Verificação dos dados enviados do formulário
        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $eventDispatcher->dispatch(JHVUserEvents::GROUP_EDIT_SUCCESS, $event);

                $groupManager->updateGroup($group);
                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('jhv_user_groups_list_' . $manager);
                    $response = new RedirectResponse($url);
                }

                $eventDispatcher->dispatch(JHVUserEvents::GROUP_EDIT_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));
                return $response;
            }
        }
        
        return $this->getTemplateRenderer()->renderResponse('group_edit', array(
            'group' => $group,
            'form'  => $form->createView()
        ));
    }

    public function deleteAction(Request $request, $groupId)
    {
        $manager        = $request->get('manager');
        $groupManager   = $this->getGroupManager($manager);

        $group          = $groupManager->findGroupBy(array(
            'id' => $groupId
        ));

        // Caso o grupo não tenha sido encontrado, irá gerar erro
        if (!$group) {
            throw new NotFoundHttpException(sprintf(
                'The group %s cannot be found.',
                $groupId
            ));
        }

        $groupManager->deleteGroup($group);
        $response = new RedirectResponse($this->container->get('router')->generate('jhv_user_groups_list_' . $manager));

        // Despachar evento de econclusão da deleção de grupos
        $this->getEventDispatcher()->dispatch(JHVUserEvents::GROUP_DELETE_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));

        return $response;
    }
    
}