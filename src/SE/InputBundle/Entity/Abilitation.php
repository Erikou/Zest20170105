<?php
namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abilitation
 *
 * @ORM\Table(name="abilitation")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\AbilitationRepository")
 */
class Abilitation
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
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_creation", type="datetime")
	 */
	private $dateCreation;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", length=255)
	 */
	private $description;
    
	/**
	 * @var Permission[]
	 * @ORM\ManyToMany(targetEntity="SE\InputBundle\Entity\Permission", cascade={"persist"})
	 * @ORM\JoinTable(
     *     name="abilitation_permission",
     *     joinColumns={@ORM\JoinColumn(name="abilitation_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")})
	 */
	private $permissions;

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
	 * @return Abilitation
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
	 * Constructor
	 */
	public function __construct()
	{
		$this->date_creation = new \Datetime("now");
	}

	/**
	 * Set date_creation
	 *
	 * @param \DateTime $dateCreation
	 * @return Activity
	 */
	public function setDateCreation($dateCreation)
	{
		$this->date_creation = $dateCreation;

		return $this;
	}

	/**
	 * Get date_creation
	 *
	 * @return \DateTime
	 */
	public function getDateCreation()
	{
		return $this->date_creation;
	}


	/**
	 * Set description
	 *
	 * @param string $name
	 * @return Abilitation
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	
		return $this;
	}
	
	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
	

	/**
	 * Add permissions
	 *
	 * @param \SE\InputBundle\Entity\Permission $permissions
	 * @return User
	 */
	public function addPermission(\SE\InputBundle\Entity\Permission $permission)
	{
		$this->permissions[] = $permission;
	
		return $this;
	}
	
	/**
	 * Remove permissions
	 *
	 * @param \SE\InputBundle\Entity\Permission $abilitations
	 */
	public function removePermission(\SE\InputBundle\Entity\Permission $permission)
	{
		$this->permissions->removeElement($permission);
	}
	
	/**
	 * Get permissions
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}
	
	public function setPermissions($perms){
		foreach ($perms as $p){
			$this->addPermission($p);
		}
	}
}
