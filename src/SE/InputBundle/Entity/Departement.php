<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Departement
 *
 * @ORM\Table(name="departement")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\DepartementRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Departement
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
     * @ORM\OneToMany(targetEntity="SE\InputBundle\Entity\Team", mappedBy="departement", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid()
     */
    private $teams;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="max_shift_nb", type="integer")
     */
    private $maxShiftNb;

    /**
     * @var integer
     *
     * @ORM\Column(name="let", type="integer")
     */
    private $let;

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
     * @return Departement
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
     * Set masterId
     *
     * @param integer $masterId
     * @return Departement
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
     * @return Departement
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
     * @return Departement
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
     * @return Departement
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
     * Constructor
     */
    public function __construct()
    {
        $this->teams = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add teams
     *
     * @param \SE\InputBundle\Entity\Team $teams
     * @return Departement
     */
    public function addTeam(\SE\InputBundle\Entity\Team $teams)
    {
        $this->teams[] = $teams;

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
     * @ORM\PostPersist
     */
    public function updateLinkedEntities()
    {
        //NOT TESTED !
        //will be used when new departement created in form (front-end action) -> admin page
        //by default, all teams will be moved into the new team (based on same masterId)
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('SEInputBundle:Team')->findBy(array('departement' => $this->masterId));
        foreach ($teams as $t) {
            $t->setDepartement($this);
        }

        $em->flush();
    }

    /**
     * @ORM\PrePersist
     */
    public function generateMaxShiftNb()
    {
        //NOT TESTED !
        //will be used when new departement created in form (front-end action) -> admin page
        $max = 1;
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('SEInputBundle:Team')->findBy(array('departement' => $this->masterId));
        foreach ($teams as $t) {
            if($t->getShiftnb() > $max){
                $max = $t->getShiftnb();
            }
        }

        $this->maxShiftNb = $max;
    }

    /**
     * Set maxShiftNb
     *
     * @param integer $maxShiftNb
     * @return Departement
     */
    public function setMaxShiftNb($maxShiftNb)
    {
        $this->maxShiftNb = $maxShiftNb;

        return $this;
    }

    /**
     * Get maxShiftNb
     *
     * @return integer 
     */
    public function getMaxShiftNb()
    {
        return $this->maxShiftNb;
    }

    /**
     * Set let
     *
     * @param integer $let
     * @return Departement
     */
    public function setLet($let)
    {
        $this->let = $let;

        return $this;
    }

    /**
     * Get let
     *
     * @return integer 
     */
    public function getLet()
    {
        return $this->let;
    }

    /**
     * Set displayOrder
     *
     * @param integer $displayOrder
     * @return Departement
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
