<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 */
class User extends AbstractEntity implements UserInterface,\Serializable
{
    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var boolean
     */
    private $isactive =1;

    /**
     * @var string
     */
    private $roles = 'ROLE_USER';

    /**
     * @var \DateTime
     */
    private $createdAt;

    /** @var  string */
    private $planPassword;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set userName
     *
     * @param string $userName
     * @return User
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string 
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isactive
     *
     * @param boolean $isactive
     * @return User
     */
    public function setIsactive($isactive)
    {
        $this->isactive = $isactive;

        return $this;
    }

    /**
     * Get isactive
     *
     * @return boolean 
     */
    public function getIsactive()
    {
        return $this->isactive;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set planPassword
     *
     * @param $planpassword
     *
     * @return User
     */
    public function setPlanPassword($planpassword)
    {
        $this->planPassword = $planpassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlanPassword()
    {
        return $this->planPassword;
    }


    //-----------------------------------------------------
    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * Set roles
     *
     * @param string $roles
     *
     * @return User
     */
    public function setRoles(array $roles)
    {
        $this->roles = implode(',', $roles);

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return explode(',', $this->roles);
    }
    /**
     * 判断用户是否拥有给定权限
     *
     * @param string $role
     * @return boolean
     */
    public function hasRole($role)
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * 追加一个权限
     *
     * @param string $role
     * @return User
     */
    public function addRole($role)
    {
        if (!$this->hasRole($role)) {
            $roles = $this->getRoles();
            $roles[] = $role;
            $this->setRoles($roles);
        }
        return $this;
    }

    /**
     * 删除一个权限
     *
     * @param string $role
     * @return User
     */
    public function removeRole($role)
    {
        $roles = $this->getRoles();
        $key = array_search($role, $roles);
        if ($key !== false) {
            unset($roles[$key]);
            $this->setRoles($roles);
        }
        return $this;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->userName,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->userName,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }
}
