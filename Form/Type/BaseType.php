<?php

namespace JHV\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * BaseType
 *
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
abstract class BaseType extends AbstractType
{
    
    protected $translationDomain;
    
    /**
     * Construtor.
     * Recebimento do parâmetro parar ser definido como domínio de tradução 
     * dos campos de formulário.
     * 
     * @param type $translationDomain
     */
    public function __construct($translationDomain)
    {
        $this->translationDomain = $translationDomain;
    }
    
    /**
     * Localizar qual o domínio para tradução dos fields
     * @return string
     */
    public function getTranslationDomain()
    {
        return $this->translationDomain;
    }
    
}