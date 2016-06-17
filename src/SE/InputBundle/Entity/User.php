<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Team")
     * @ORM\JoinColumn(nullable=true)
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Shift")
     * @ORM\JoinColumn(nullable=true)
     */
    private $shift;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;
    private $plainPassword;
    
	/**
	 * @var Abilitation[]
	 * @ORM\ManyToMany(targetEntity="SE\InputBundle\Entity\Abilitation", cascade={"persist"})
	 * @ORM\JoinTable(
     *     name="user_abilitation",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="abilitation_id", referencedColumnName="id")})
	 */
	private $abilitations;
	
	public function _construct(){
		$this->abilitations = array();
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
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set team
     *
     * @param \SE\InputBundle\Entity\Team $team
     * @return User
     */
    public function setTeam(\SE\InputBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \SE\InputBundle\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set shift
     *
     * @param \SE\InputBundle\Entity\Shift $shift
     * @return User
     */
    public function setShift(\SE\InputBundle\Entity\Shift $shift = null)
    {
        $this->shift = $shift;

        return $this;
    }

    /**
     * Get shift
     *
     * @return \SE\InputBundle\Entity\Shift 
     */
    public function getShift()
    {
        return $this->shift;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
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
    
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

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
    
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        return null;
    }

    /**
     * Add abilitations
     *
     * @param \SE\InputBundle\Entity\Abilitation $abilitations
     * @return User
     */
    public function addAbilitation(\SE\InputBundle\Entity\Abilitation $abilitations)
    {
    	$this->abilitations[] = $abilitations;
    
    	return $this;
    }
    
    /**
     * Remove abilitations
     *
     * @param \SE\InputBundle\Entity\Abilitation $abilitations
     */
    public function removeAbilitation(\SE\InputBundle\Entity\Abilitation $abilitations)
	{
		$this->abilitations->removeElement($abilitations);
	}

	/**
	 * Get abilitations
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
    public function getAbilitations()
    {
    	return $this->abilitations;
    }
    
    /**
     * Returns the roles granted to the user.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
    	$arr = array();
    	$arr[] = "ROLE_USER";
    	try {
    		foreach ($this->abilitations as $value){
    			foreach($value->getPermissions() as $value2){ $arr[] = $value2->getName(); } }
    	} catch (\Exception $e) {
    		if ($e instanceof HandledErrorException) {
    			$e->cleanOutput();
			}
    		$this->abilitations = array();
    	}
        return $arr;
    }
    
    public function setRoles($roles){
    	if (is_array($roles)){
    		foreach ($roles as $ab){
    			$this->addAbilitation($ab);
    		}
    	}
    }
    
    public function getRolesDescription(){
    	$arr = array();
    	foreach ($this->abilitations as $i => $value){
    		$arr[] = $this->abilitations[$i]->getName();
    		foreach($this->abilitations[$i]->permissions as $j => $value2){
    			$arr[] = $this->abilitations[$i]->permissions[$j]->getName();
    		}
    	}
    	return implode($arr);
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->name,
            // $this->password,
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->name,
            // $this->password,
            // $this->salt
            ) = unserialize($serialized);
    }
}
