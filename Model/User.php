<?php

namespace JHV\Bundle\UserBundle\Model;

use JHV\Bundle\UserBundle\Model\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @copyright (c) 2013, Jorge Vahldick
 * @license MIT on Resources/meta/LICENSE
 */
abstract class User implements UserInterface, GroupableInterface
{
    
    /**
     * @var string
     */
    protected $confirmationToken;
    
    /**
     * @var string
     */
    protected $email;
    
    /**
     * @var string 
     */
    protected $emailCanonical;
    
    /**
     * @var string 
     */
    protected $plainPassword;
    
    /**
     * @var string 
     */
    protected $password;
    
    /**
     * @var array
     */
    protected $roles;
    
    /**
     * @var string 
     */
    protected $salt;
    
    /**
     * @var string 
     */
    protected $username;
    
    /**
     * @var string 
     */
    protected $usernameCanonical;
    
    /**
     * @var boolean
     */
    protected $enabled;
    
    /**
     * @var boolean
     */
    protected $locked;
    
    /**
     * @var boolean
     */
    protected $accountExpired;
    
    /**
     * @var boolean
     */
    protected $credentialsExpired;
    
    /**
     * @var \DateTime 
     */
    protected $accountExpiresAt;
    
    /**
     * @var \DateTime 
     */
    protected $credentialsExpiresAt;
    
    /**
     * @var \DateTime 
     */
    protected $createdAt;
    
    /**
     * @var \DateTime 
     */
    protected $updatedAt;
    
    /**
     * @var \DateTime 
     */
    protected $lastLoginAt;
    
    /**
     * @var \DateTime 
     */
    protected $passwordRequestedAt;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $groups;
    
    /**
     * Constructor.
     * Definições de parâmetros base para o objeto.
     */
    public function __construct()
    {
        $this->roles    = array();
        $this->salt     = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled  = true;
        $this->locked   = false;
        
        // Auxiliares para definir manualmente conta como expirada ou com senha expirada
        $this->accountExpired = false;
        $this->credentialsExpired = false;
    }
    
    /**
     * Localizar o identificador do usuário.
     * Este método deverá ser implementado pela entidade.
     */
    public abstract function getId();
    
    public function isAccountNonExpired()
    {
        $isExpired = true;        
        if (true === $this->accountExpired || (null !== $this->accountExpiresAt && $this->accountExpiresAt->getTimestamp() < time())) {
            $isExpired = false;
        }

        return $isExpired;
    }
    
    public function isAccountNonLocked()
    {
        return false === $this->isLocked();
    }
    
    public function isCredentialsNonExpired()
    {
        $isCredentialsExpired = true;
        if (true === $this->credentialsExpired || (null !== $this->credentialsExpiresAt && $this->credentialsExpiresAt->getTimestamp() < time())) {
            $isCredentialsExpired = false;
        }

        return $isCredentialsExpired;
    }

    public function setConfirmationToken($token)
    {
        $this->confirmationToken = $token;
        return $this;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    
    public function setEmailCanonical($email)
    {
        $this->emailCanonical = $email;
        return $this;
    }
    
    public function setEnabled($boolean)
    {
        $this->enabled = (Boolean) $boolean;
        return $this;
    }
    
    public function setAccountExpired($boolean)
    {
        $this->accountExpired = (Boolean) $boolean;
        return $this;
    }
    
    public function setLocked($boolean)
    {
        $this->locked = (Boolean) $boolean;
        return $this;
    }
    
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
        return $this;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    
    public function setUsernameCanonical($username)
    {
        $this->usernameCanonical = $username;
        return $this;
    }
    
    public function setLastLoginAt(\DateTime $datetime)
    {
        $this->lastLoginAt = $datetime;
        return $this;
    }
    
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }
    
    public function setPasswordRequestedAt(\DateTime $datetime = null)
    {
        $this->passwordRequestedAt = $datetime;
        return $this;
    }
    
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    public function isAccountExpired()
    {
        return false === $this->isAccountNonExpired();
    }
    
    public function isCredentialsExpired()
    {
        return false === $this->isCredentialsNonExpired();
    }
    
    public function isLocked()
    {
        return $this->locked;
    }
    
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function getEmail()
    {
        return $this->email;
    }
    
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }
    
    public function getGroupNames()
    {
        $names = array();
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }
        
        return $names;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        
        // Verificação dos grupos
        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }
        
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    public function getUsername()
    {
        return $this->username;
    }
    
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function isPasswordRequestNonExpired($timeToLive)
    {
        return  
            $this->passwordRequestedAt instanceof \DateTime &&
            $this->passwordRequestedAt->getTimestamp() + $timeToLive > time();
        ;
    }
    
    public function getSalt()
    {
        return $this->salt;
    }
    
    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }
    
    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }
    
    public function setAccountExpiresAt(\DateTime $expirationDate)
    {
        $this->accountExpiresAt = $expirationDate;
        return $this;
    }
    
    public function getAccountExpiresAt()
    {
        return $this->accountExpiresAt;
    }
    
    public function setCredentialsExpiresAt(\DateTime $expirationDate)
    {
        $this->credentialsExpiresAt = $expirationDate;
        return $this;
    }
    
    public function getCredentialsExpiresAt()
    {
        return $this->credentialsExpiresAt;
    }
    
    public function addRole($role)
    {
        $role = strtoupper($role);
        if (false === in_array($role, $this->roles) && static::ROLE_DEFAULT !== $role) {
            $this->roles[] = $role;
        }
        
        return $this;
    }
    
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function setRoles($roles)
    {
        $this->roles = array();
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        
        return $this;
    }
    
    public function isEqualTo(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        return $this->getId() === $user->getId();
    }
    
    public function isUser(UserInterface $user = null)
    {
        return null !== $user && $this->isEqualTo($user);
    }
    
    public function isSuperAdmin()
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }
    
    public function setSuperAdmin($boolean)
    {
        if (true === (Boolean) $boolean) {
            $this->addRole(self::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole (self::ROLE_SUPER_ADMIN);
        }
        
        return $this;
    }
    
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    
    public function serialize()
    {
        return serialize(array(            
            $this->getId(),
            $this->username,
            $this->password,
            $this->salt,
            $this->enabled,
            $this->locked,
        ));
    }
    
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
            $this->enabled,
            $this->locked,
        ) = unserialize($serialized);
    }
    
    public function __toString()
    {
        return (string) $this->getUsername();
    }
    
}