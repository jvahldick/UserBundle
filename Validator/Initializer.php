<?php

namespace JHV\Bundle\UserBundle\Validator;

use Symfony\Component\Validator\ObjectInitializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use JHV\Bundle\UserBundle\Manager\User\Helper\UserHelperInterface;

/**
 * Initializer
 *
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class Initializer implements ObjectInitializerInterface
{
    
    protected $helper;
    
    public function __construct(UserHelperInterface $helper)
    {
        $this->helper = $helper;
    }
    
    public function initialize($object)
    {        
        if ($object instanceof UserInterface) {
            $this->helper->updateCanonicalFields($object);
        }
    }
    
}