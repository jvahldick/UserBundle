<?php

namespace JHV\Bundle\UserBundle\Util;

/**
 * CanonicalizerInterface
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface CanonicalizerInterface
{
    
    /**
     * Canonizalicar uma string.
     * Objetivo de utilizar para um padrão de busca.
     * 
     * @param string $string
     * @return string
     */
    function canonicalize($string);
    
}