<?php

namespace JHV\Bundle\UserBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\User\UserInterface;
use JHV\Bundle\UserBundle\Manager\User\Helper\UserHelperInterface;

class UserListener
{

    protected $helper;

    public function __construct(UserHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof UserInterface) {
            $this->helper->updateCanonicalFields($entity);
            $this->helper->updatePassword($entity);
            
            $entity->setCreatedAt(new \DateTime());
            $entity->setUpdatedAt(new \DateTime());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof UserInterface) {
            $this->helper->updateCanonicalFields($entity);
            $this->helper->updatePassword($entity);
            $entity->setUpdatedAt(new \DateTime());
            
            $em   = $args->getEntityManager();
            $uow  = $em->getUnitOfWork();
            $meta = $em->getClassMetadata(get_class($entity));
            $uow->recomputeSingleEntityChangeSet($meta, $entity);
        }
    }
}