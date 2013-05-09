<?php

namespace JHV\Bundle\UserBundle\Twig\Extension;

/**
 * UserExtension
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class UserExtension extends \Twig_Extension
{
    
    protected $translationDomain;
    
    public function __construct($translationDomain)
    {
        $this->translationDomain = $translationDomain;
    }
    
    public function getFunctions()
    {
        return array(
            'jhv_user_get_translation_domain' => new \Twig_Function_Method($this, 'getTraslationDomain')
        );
    }

    public function getTraslationDomain()
    {
        return $this->translationDomain;
    }
    
    public function getName()
    {
        return 'jhv_user_extension';
    }
    
}