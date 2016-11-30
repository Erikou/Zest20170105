<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Activity
 *
 * @ORM\Table(name="activity")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\ActivityRepository")
 */
class Activity
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
     * @var double
     *
     * @ORM\Column(name="default_target", type="decimal", nullable=true)
     */
    private $default_target;

    /**
     * @ORM\ManyToMany(targetEntity="SE\InputBundle\Entity\Team", inversedBy="activities", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $teams;

    /**
     * @var boolean
     *
     * @ORM\Column(name="productive", type="boolean")
     */
    private $productive;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="trackable", type="boolean")
     */
    private $trackable = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fictive", type="boolean")
     */
    private $fictive = false;

    /**
     * @ORM\OneToMany(targetEntity="SE\InputBundle\Entity\ActivityZone", mappedBy="activity", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $activity_zones;

    /**
     * @ORM\Column(name="date_creation", type="date")
     */
    protected $date_creation;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var integer
     * @Assert\NotNull()
     * @ORM\Column(name="master_id", type="integer", nullable=false)
     */
    private $masterId;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="start_date", type="date", nullable=false)
     */
    private $startDate;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="end_date", type="date", nullable=true)
     */
    private $endDate = null;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status_control", type="boolean")
     */
    private $statusControl = 1;

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
     * @return Activity
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
        $this->date_creation = new \Datetime();

    }

    /**
     * Set default_target
     *
     * @param double $defaultTarget
     * @return Activity
     */
    public function setDefaultTarget($defaultTarget = null)
    {
        $this->default_target = $defaultTarget;

        return $this;
    }

    /**
     * Get default_target
     *
     * @return double 
     */
    public function getDefaultTarget()
    {
        return $this->default_target;
    }

    /**
     * Add activity_zones
     *
     * @param \SE\InputBundle\Entity\ActivityZone $activityZones
     * @return Activity
     */
    public function addActivityZone(\SE\InputBundle\Entity\ActivityZone $activityZones)
    {
        $this->activity_zones[] = $activityZones;
        $activityZones->setActivity($this);

        return $this;
    }

    /**
     * Remove activity_zones
     *
     * @param \SE\InputBundle\Entity\ActivityZone $activityZones
     */
    public function removeActivityZone(\SE\InputBundle\Entity\ActivityZone $activityZones)
    {
        $this->activity_zones->removeElement($activityZones);
        $activity_zones->setActivity(null);
    }

    /**
     * Get activity_zones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivityZones()
    {
        return $this->activity_zones;
    }

    /**
     * Add teams
     *
     * @param \SE\InputBundle\Entity\Team $teams
     * @return Activity
     */
    public function addTeam(\SE\InputBundle\Entity\Team $teams)
    {
        $this->teams[] = $teams;
        $teams->addActivity($this);

        return $this;
    }

    /**
     * Remove teams
     *
     * @param \SE\InputBundle\Entity\Team $teams
     */
    public function removeTeam(\SE\InputBundle\Entity\Team $teams)
    {
        $this->teams->removeElement($teams);
    }

    /**
     * Get teams
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Set productive
     *
     * @param boolean $productive
     * @return Activity
     */
    public function setProductive($productive)
    {
        $this->productive = $productive;

        return $this;
    }

    /**
     * Get productive
     *
     * @return boolean 
     */
    public function getProductive()
    {
        return $this->productive;
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Activity
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
    }

    /**
     * Set trackable
     *
     * @param boolean $trackable
     * @return Activity
     */
    public function setTrackable($trackable)
    {
        $this->trackable = $trackable;

        return $this;
    }

    /**
     * Get trackable
     *
     * @return boolean 
     */
    public function getTrackable()
    {
        return $this->trackable;
    }


    /**
     * Set fictive
     *
     * @param boolean $fictive
     * @return Activity
     */
    public function setFictive($fictive)
    {
        $this->fictive = $fictive;

        return $this;
    }

    /**
     * Get fictive
     *
     * @return boolean 
     */
    public function getFictive()
    {
        return $this->fictive;
    }

    /**
     * Set masterId
     *
     * @param integer $masterId
     * @return Activity
     */
    public function setMasterId($masterId)
    {
        $this->masterId = $masterId;

        return $this;
    }

    /**
     * Get masterId
     *
     * @return integer 
     */
    public function getMasterId()
    {
        return $this->masterId;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Activity
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Activity
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set statusControl
     *
     * @param boolean $statusControl
     * @return Activity
     */
    public function setStatusControl($statusControl)
    {
        $this->statusControl = $statusControl;

        return $this;
    }

    /**
     * Get statusControl
     *
     * @return boolean 
     */
    public function getStatusControl()
    {
        return $this->statusControl;
    }
}
