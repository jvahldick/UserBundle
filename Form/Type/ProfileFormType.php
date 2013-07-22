<?php

namespace JHV\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * ProfileFormType
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class ProfileFormType extends BaseType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraint = new UserPassword();
        $this->buildUserForm($builder, $options);

        $builder->add('current_password', 'password', array(
            'label'                 => 'form.label.current_password',
            'translation_domain'    => $this->getTranslationDomain(),
            'mapped'                => false,
            'constraints'           => $constraint,
        ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'intention'         => 'profile',
            'change_username'   => true
        ));
        
        // Para não haver duplicação de serviços de formulário, exigir classe de usuário
        $resolver
            ->setRequired(array(
                'data_class'
            ))
        ;
        
        // Definir data_class como aceitação para string
        $resolver->setAllowedTypes(array(
            'data_class'        => 'string'
        ));
    }
    
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $defaultUserOptions = array('label' => 'form.label.username', 'translation_domain' => $this->getTranslationDomain());
        if (isset($options['change_username']) && false === $options['change_username']) {
            $defaultUserOptions['read_only'] = true;
            $defaultUserOptions['disabled'] = true;
        }

        $builder->add('username', null, $defaultUserOptions);
        $builder->add('email', 'email', array('label' => 'form.label.email', 'translation_domain' => $this->getTranslationDomain()));
    }
    
    public function getName()
    {
        return 'jhv_user_profile_type';
    }
    
}