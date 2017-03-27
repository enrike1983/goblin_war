<?php

namespace UserBundle\Entity;

use AppBundle\Manager\NavigationManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="gw_users")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface, \Serializable
{
    const MAX_LIFE = 100;
    const MIN_ATTACK = 30;
    const MAX_ATTACK = 50;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="integer")
     */
    private $life = self::MAX_LIFE;

    /**
     * @ORM\Column(type="integer")
     */
    private $attack;

    /**
     * @ORM\Column(type="array")
     */
    private $position = NavigationManager::INITIAL_POSITION;

    /**
     * @ORM\Column(type="float")
     */
    private $experience = 0;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Monster", mappedBy="user", cascade={"persist", "remove"})
     */
    private $monster;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $apiKey;


    public function __construct()
    {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->username;
    }

    /**
     * @ORM\PrePersist
     */
    public function presetValues()
    {
        $this->attack = mt_rand(self::MIN_ATTACK, self::MAX_ATTACK);
        $this->apiKey = bin2hex(random_bytes(22));
    }

    public function getUsername()
    {
        return $this->username;
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
        return array('ROLE_USER');
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param $password
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * Set email
     *
     * @param string $email
     *
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set life
     *
     * @param string $life
     *
     * @return User
     */
    public function setLife($life)
    {
        $this->life = $life;

        return $this;
    }

    /**
     * Get life
     *
     * @return string
     */
    public function getLife()
    {
        return $this->life;
    }

    /**
     * Set attack
     *
     * @param string $attack
     *
     * @return User
     */
    public function setAttack($attack)
    {
        $this->attack = $attack;

        return $this;
    }

    /**
     * Get attack
     *
     * @return string
     */
    public function getAttack()
    {
        return $this->attack;
    }

    /**
     * Set position
     *
     * @param \array $position
     *
     * @return User
     */
    public function setPosition(array $position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return \array
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set monster
     *
     * @param \AppBundle\Entity\Monster $monster
     *
     * @return User
     */
    public function setMonster(\AppBundle\Entity\Monster $monster = null)
    {
        $this->monster = $monster;

        return $this;
    }

    /**
     * Get monster
     *
     * @return \AppBundle\Entity\Monster
     */
    public function getMonster()
    {
        return $this->monster;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return User
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set experience
     *
     * @param float $experience
     *
     * @return User
     */
    public function setExperience($experience)
    {
        $this->experience = $experience;

        return $this;
    }

    /**
     * Get experience
     *
     * @return float
     */
    public function getExperience()
    {
        return $this->experience;
    }
}
