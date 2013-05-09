<?php

namespace JHV\Bundle\UserBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * LoginManager
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class LoginManager implements LoginManagerInterface
{
    
    protected $securityContext;
    protected $userChecker;
    protected $sessionStrategy;
    protected $container;
    
    public function __construct(SecurityContextInterface $context, UserCheckerInterface $userChecker, SessionAuthenticationStrategyInterface $sessionStrategy, ContainerInterface $container)
    {
        $this->securityContext  = $context;
        $this->userChecker      = $userChecker;
        $this->sessionStrategy  = $sessionStrategy;
        $this->container        = $container;
    }
    
    final public function loginUser(UserInterface $user, $firewall, Response $response = null)
    {
        $this->userChecker->checkPostAuth($user);
        $token = $this->createToken($user, $firewall);

        if ($this->container->isScopeActive('request')) {
            $this->sessionStrategy->onAuthentication($this->container->get('request'), $token);

            if (null !== $response) {
                $rememberMeServices = null;
                if ($this->container->has('security.authentication.rememberme.services.persistent.'.$firewall)) {
                    $rememberMeServices = $this->container->get('security.authentication.rememberme.services.persistent.'.$firewall);
                } elseif ($this->container->has('security.authentication.rememberme.services.simplehash.'.$firewall)) {
                    $rememberMeServices = $this->container->get('security.authentication.rememberme.services.simplehash.'.$firewall);
                }

                if ($rememberMeServices instanceof RememberMeServicesInterface) {
                    $rememberMeServices->loginSuccess($this->container->get('request'), $response, $token);
                }
            }
        }

        $this->securityContext->setToken($token);
    }
    
    public function createToken(UserInterface $user, $firewall)
    {
        return new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
    }
    
}