<?php

namespace JHV\Bundle\UserBundle\Util;

use JHV\Bundle\UserBundle\Exception\ExtensionNotLoadedException;

/**
 * Canonicalizer
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class Canonicalizer implements CanonicalizerInterface
{
    
    public function __construct()
    {
        if (false === extension_loaded('mbstring')) {
            throw new ExtensionNotLoadedException(sprintf(
                'The extension %s must be enabled.',
                'mbstring'
            ));
        }
    }
    
    public function canonicalize($string)
    {
        return mb_convert_case($string, MB_CASE_LOWER, mb_detect_encoding($string));
    }
    
}