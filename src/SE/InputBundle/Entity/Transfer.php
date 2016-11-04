<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Transfer
 *
 * @ORM\Table(name="transfers")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\TransferRepository")
 */
class Transfer
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
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $demand;
    
    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $decision;
    
    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Employee")
     * @ORM\JoinColumn(nullable=true)
     */
    private $employee;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Departement")
     * @ORM\JoinColumn(nullable=true)
     */
    private $departement;

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
     * @var integer
     *
     * @ORM\Column(name="validated", type="integer")
     */
    private $validated;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="date_start", type="date", nullable=true)
     */
    private $date_start;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_hours", type="integer")
     */
    private $total_hours;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Activity")
     * @ORM\JoinColumn(nullable=true)
     */
    private $activity;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\InputEntry")
     * @ORM\JoinColumn(nullable=true)
     */
    private $inputentry;

    public function __construct()
    {
        $this->date_start = new \Datetime();
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
     * Set date_start
     *
     * @param \DateTime $dateStart
     * @return Transfer
     */
    public function setDateStart($dateStart)
    {
        $this->date_start = $dateStart;

        return $this;
    }

    /**
     * Get date_start
     *
     * @return \DateTime 
     */
    public function getDateStart()
    {
        return $this->date_start;
    }
    public function getDateStartString()
    {
        return $this->date_start->format('d/m/Y');
    }

    /**
     * Set employee
     *
     * @param \SE\InputBundle\Entity\Employee $employee
     * @return Transfer
     */
    public function setEmployee(\SE\InputBundle\Entity\Employee $employee = null)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \SE\InputBundle\Entity\Employee 
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set demand
     *
     * @param \SE\InputBundle\Entity\User $demand
     * @return Transfer
     */
    public function setDemand(\SE\InputBundle\Entity\User $demand = null)
    {
        $this->demand = $demand;

        return $this;
    }

    /**
     * Get demand
     *
     * @return \SE\InputBundle\Entity\User 
     */
    public function getDemand()
    {
        return $this->demand;
    }

    /**
     * Set decision
     *
     * @param \SE\InputBundle\Entity\User $decision
     * @return Transfer
     */
    public function setDecision(\SE\InputBundle\Entity\User $decision = null)
    {
        $this->decision = $decision;

        return $this;
    }

    /**
     * Get decision
     *
     * @return \SE\InputBundle\Entity\User 
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * Set departement
     *
     * @param \SE\InputBundle\Entity\Departement $departement
     * @return Transfer
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
     * Set team
     *
     * @param \SE\InputBundle\Entity\Team $team
     * @return Transfer
     */
    public function setTeam(\SE\InputBundle\Entity\Team $team)
    {
        $this->team = $team;
        $this->departement = $team->getDepartement();

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
     * @return Transfer
     */
    public function setShift(\SE\InputBundle\Entity\Shift $shift)
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
     * Set validated
     *
     * @param integer $validated
     * @return Transfer
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Get validated
     *
     * @return integer 
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * Set total_hours
     *
     * @param integer $total_hours
     * @return Transfer
     */
    public function setTotalHours($total_hours)
    {
        $this->total_hours = $total_hours;

        return $this;
    }

    /**
     * Get total_hours
     *
     * @return integer 
     */
    public function getTotalHours()
    {
        return $this->total_hours;
    }


    /**
     * Set activity
     *
     * @param \SE\InputBundle\Entity\Activity $activity
     * @return Transfer
     */
    public function setActivity(\SE\InputBundle\Entity\Activity $activity = null)
    {
    	$this->activity = $activity;
    
    	return $this;
    }
    
    /**
     * Get activity
     *
     * @return \SE\InputBundle\Entity\Activity
     */
    public function getActivity()
    {
    	return $this->activity;
    }

    /**
     * Set inputentry
     *
     * @param \SE\InputBundle\Entity\InputEntry $inputentry
     * @return Transfer
     */
    public function setInputEntry(\SE\InputBundle\Entity\InputEntry $inputentry = null)
    {
        $this->inputentry = $inputentry;

        return $this;
    }

    /**
     * Get inputentry
     *
     * @return \SE\InputBundle\Entity\InputEntry 
     */
    public function getInputEntry()
    {
        return $this->inputentry;
    }
}
