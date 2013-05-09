<?php

namespace JHV\Bundle\UserBundle\Manager\User\Helper;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use JHV\Bundle\UserBundle\Util\CanonicalizerInterface;

/**
 * UserHelper
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class UserHelper implements UserHelperInterface
{
    
    protected $encoder;
    protected $canonicalizer;
    
    /**
     * Construtor.
     * 
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param \JHV\Bundle\UserBundle\Util\CanonicalizerInterface $canonicalizer
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $canonicalizer)
    {
        $this->encoder = $encoderFactory;
        $this->canonicalizer = $canonicalizer;
    }
    
    public function canonicalize($string)
    {
        return $this->canonicalizer->canonicalize($string);
    }
    
    public function updateCanonicalFields(UserInterface $user)
    {
        $user->setUsernameCanonical($this->canonicalizer->canonicalize($user->getUsername()));
        $user->setEmailCanonical($this->canonicalizer->canonicalize($user->getEmail()));
    }
    
    public function updatePassword(UserInterface $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $user->setPassword($this->encoder->getEncoder($user)->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }
    
}