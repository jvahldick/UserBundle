<?php

namespace JHV\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * RegistrationFormType
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class RegistrationFormType extends BaseType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.label.username', 'translation_domain' => $this->getTranslationDomain()))
            ->add('email', 'email', array('label' => 'form.label.email', 'translation_domain' => $this->getTranslationDomain()))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => $this->getTranslationDomain()),
                'first_options' => array('label' => 'form.label.password'),
                'second_options' => array('label' => 'form.label.password_confirmation'),
                'invalid_message' => 'form.error.password_confirmation',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'intention'  => 'registration',
        ));
        
        // Para não haver duplicação de serviços de formulário, exigir classe de usuário
        $resolver
            ->setRequired(array(
                'data_class'
            ))
        ;
        
        // Definir data_class como aceitação para string
        $resolver->setAllowedTypes(array(
            'data_class' => 'string',
        ));
    }

    public function getName()
    {
        return 'jhv_user_registration_type';
    }
    
}