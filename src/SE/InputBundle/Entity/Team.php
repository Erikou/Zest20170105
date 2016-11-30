<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\TeamRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Team
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
     * @var integer
     *
     * @ORM\Column(name="shiftnb", type="integer")
     */
    private $shiftnb;

    /**
     * @ORM\ManyToMany(targetEntity="SE\InputBundle\Entity\Activity", mappedBy="teams", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="SE\InputBundle\Entity\Employee", mappedBy="default_team", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $employees;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Departement", inversedBy="teams", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $departement;

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
     * @var integer
     * @Assert\NotNull()
     * @ORM\Column(name="display_order", type="integer", nullable=false)
     */
    private $displayOrder;


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
     * @return Team
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
     * Set shiftnb
     *
     * @param integer $shiftnb
     * @return Team
     */
    public function setShiftnb($shiftnb)
    {
        $this->shiftnb = $shiftnb;

        return $this;
    }

    /**
     * Get shiftnb
     *
     * @return integer 
     */
    public function getShiftnb()
    {
        return $this->shiftnb;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add activities
     *
     * @param \SE\InputBundle\Entity\Activity $activities
     * @return Team
     */
    public function addActivity(\SE\InputBundle\Entity\Activity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \SE\InputBundle\Entity\Activity $activities
     */
    public function removeActivity(\SE\InputBundle\Entity\Activity $activities)
    {
        $this->activities->removeElement($activities);
        $activities->removeTeam($this);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Set masterId
     *
     * @param integer $masterId
     * @return Team
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
     * @return Team
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
     * @return Team
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
     * @return Team
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

    /**
     * Set departement
     *
     * @param \SE\InputBundle\Entity\Departement $departement
     * @return Team
     */
    public function setDepartement(\SE\InputBundle\Entity\Departement $departement)
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * Get departement
     *
     * @return \SE\InputBundle\Entity\Departement 
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Add employees
     *
     * @param \SE\InputBundle\Entity\Team $employees
     * @return Team
     */
    public function addEmployee(\SE\InputBundle\Entity\Team $employees)
    {
        $this->employees[] = $employees;

        return $this;
    }

    /**
     * Remove employees
     *
     * @param \SE\InputBundle\Entity\Team $employees
     */
    public function removeEmployee(\SE\InputBundle\Entity\Team $employees)
    {
        $this->employees->removeElement($employees);
    }

    /**
     * Get employees
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * @ORM\PostPersist
     */
    public function updateLinkedEntities()
    {
        //NOT TESTED !
        //will be used when new team created in form (front-end action) -> admin page
        //by default, all employees,users,activities will be moved into the new team (based on same masterId)
        $em = $this->getDoctrine()->getManager();
        $employees = $em->getRepository('SEInputBundle:Employee')->findBy(array('default_team' => $this->masterId));
        foreach ($employees as $e) {
            $e->setDefaultTeam($this);
        }

        $users = $em->getRepository('SEInputBundle:User')->findBy(array('team' => $this->masterId));    
        foreach ($users as $u) {
            $u->setTeam($this);
        }
        
        $activities = $em->getRepository('SEInputBundle:Activity')->getTeamsActivities($this->masterId);
        foreach ($activities as $a) {
            foreach ($a->getTeams() as $t) {
                if($t->getId() == $this->masterId){
                    $a->removeTeam($t);
                    $a->addTeam($this);
                }
            }
        }

        $em->flush();
    }

    /**
     * Set displayOrder
     *
     * @param integer $displayOrder
     * @return Team
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    /**
     * Get displayOrder
     *
     * @return integer 
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }
}
