<?php

namespace JHV\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraint\UserPassword;

/**
 * ChangePasswordFormType
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class ChangePasswordFormType extends BaseType
{
    
    protected $translationDomain = 'JHVUserBundle';
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraint = new UserPassword();
        
        $builder->add('current_password', 'password', array(
            'label'                 => 'form.label.current_password',
            'translation_domain'    => $this->translationDomain,
            'mapped'                => false,
            'constraints'           => $constraint,
        ));
        
        $builder->add('plainPassword', 'repeated', array(
            'type'                  => 'password',
            'options'               => array('translation_domain' => $this->translationDomain),
            'first_options'         => array('label' => 'form.label.new_password'),
            'second_options'        => array('label' => 'form.label.new_password_confirmation'),
            'invalid_message'       => 'form.error.password_confirmation',
        ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'intention'  => 'change_password',
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
        return 'jhv_user_change_password_type';
    }
    
}