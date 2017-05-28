<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 *
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_users")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true, nullable=true)
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true, nullable=true)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @var bool
     */
    private $isActive;

    /**
     * @ORM\Column(name="twitcasting_user_id", type="string", length=60, unique=true, nullable=true)
     * @var string|null
     */
    private $twitcastingUserId;

    /**
     * @ORM\Column(name="twitcasting_token", type="string", length=2048, nullable=true)
     * @var string|null
     */
    private $twitcastingToken;


    public function __construct()
    {
        $this->isActive = true;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return ['ROLE_ADMIN'];
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTwitcastingUserId()
    {
        return $this->twitcastingUserId;
    }

    /**
     * @param mixed $twitcastingUserId
     * @return User
     */
    public function setTwitcastingUserId($twitcastingUserId)
    {
        $this->twitcastingUserId = $twitcastingUserId;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getTwitcastingToken()
    {
        return $this->twitcastingToken;
    }

    /**
     * @param null|string $twitcastingToken
     * @return User
     */
    public function setTwitcastingToken($twitcastingToken)
    {
        $this->twitcastingToken = $twitcastingToken;
        return $this;
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }
}
