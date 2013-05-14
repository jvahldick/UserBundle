<?php

namespace JHV\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * GroupFormType
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class GroupFormType extends BaseType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'label'                 => 'form.label.group_name', 
            'translation_domain'    => $this->getTranslationDomain(),
        ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'intention'  => 'profile',
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
        return 'jhv_user_group_type';
    }
    
}
