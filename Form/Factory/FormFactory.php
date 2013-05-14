<?php

namespace JHV\Bundle\UserBundle\Form\Factory;

use Symfony\Component\Form\FormFactoryInterface;

/**
 * FormFactory
 *
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class FormFactory {
    
    protected $formFactory;
    protected $name;
    protected $type;
    protected $validationGroups;
    
    public function __construct(FormFactoryInterface $formFactory, $name, $type, $validationGroups = null)
    {
        $this->formFactory = $formFactory;
        $this->name = $name;
        $this->type = $type;
        $this->validationGroups = $validationGroups;
    }
    
    public function createForm()
    {
        return $this->formFactory->createNamed($this->name, $this->type, null, array(
            'validation_groups' => $this->validationGroups
        ));
    }
    
}