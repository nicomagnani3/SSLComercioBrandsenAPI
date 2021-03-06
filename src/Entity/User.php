<?php
 
namespace App\Entity;
 
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="usuarios");
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository");
 * @ORM\HasLifecycleCallbacks()
 */
//implements UserInterface
class User implements UserInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
 
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * 
     */
    protected $email;
 
    /**
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    protected $username;
 
 
    /**
     * @ORM\Column(name="password", type="string", length=255)
     * @Serializer\Exclude()
     */
    protected $password;
 
    /**
     * @var string
     */
    protected $plainPassword;
 
    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json")
     */
    protected $roles = [];


    /**
     * @var array
     *
     * @ORM\Column(name="grupos", type="json")
     */
    protected $grupos = [];
 
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
 
    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;


 
    public function __construct()
    {
    }
 
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
 
        return $this;
    }
 

 

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
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
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }
 
    /**
     * @param mixed $username
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;
 
        return $this;
    }
 
    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }
 
    /**
     * @param mixed $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;
 
        return $this;
    }
 
    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
 
    /**
     * @param $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
 
        $this->password = null;
    }
 
    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {

        if (!$roles) {
            $roles =  ["ROLE_USER"];
        }
        $this->roles = $roles;
 
        return $this;
    }
 
    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return ["ROLE_USER"];
    }

     /**
     * Set grupos
     *
     * @param array $grupos
     *
     * @return User
     */
    public function setGrupos($grupos)
    {

        $this->grupos = $grupos;
 
        return $this;
    }

      /**
     * Add grupos
     *
     * @param array $grupos
     *
     * @return User
     */
    public function addGrupos($grupos)
    {
        
        $this->grupos[] = $grupos; 
        $this->grupos = array_unique($this->grupos, SORT_REGULAR);
 
        return $this;
    }
 
    /**
     * Get roles
     *
     * @return array
     */
    public function getGrupos()
    {
        return $this->grupos;
    }

  
 
 
    public function eraseCredentials() {}
 
    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
 
    /**
     * @param mixed $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
 
        return $this;
    }
 
    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
 
    /**
     * @param mixed $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
 
        return $this;
    }
 
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $dateTimeNow = new DateTime('now');
        $this->setUpdatedAt($dateTimeNow);
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }


    // public function serialize()
    // {
    //    return serialize($this->getId());
    // }

    //  public function unserialize($data)
    // {
    //     $this->id = unserialize($data);
    // }
 
}