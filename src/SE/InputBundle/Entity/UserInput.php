<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserInput
 *
 * @ORM\Table(name="userinput")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\UserInputRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserInput
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
     * @ORM\OneToMany(targetEntity="SE\InputBundle\Entity\InputEntry", mappedBy="user_input", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $input_entries;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="date_input", type="datetime")
     */
    private $dateInput;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Team")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Shift")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shift;

    /**
     * @ORM\Column(name="updated_at", type="datetime", length=255, nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(name="total_hours_input", type="integer", nullable=false)
     */
    private $totalHoursInput = 0;

    /**
     * @ORM\Column(name="total_working_hours_input", type="integer", nullable=false)
     */
    private $totalWorkingHoursInput = 0;

    /**
     * @ORM\Column(name="total_overtime_input", type="decimal", nullable=false)
     */
    private $totalOvertimeInput = 0;

    /**
     * @ORM\Column(name="total_to_input", type="integer", nullable=true)
     */
    private $totalToInput = 0;

    /**
     * @ORM\Column(name="total_prod_input", type="decimal", nullable=true)
     */
    private $totalProdInput = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="process", type="boolean", nullable=true)
     */
    private $process;

    /**
     * @var boolean
     *
     * @ORM\Column(name="review", type="boolean", nullable=true)
     */
    private $review;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
     $this->date = new \Datetime();
     $this->input_entries = new \Doctrine\Common\Collections\ArrayCollection();
    }



    /**
     * Set date
     *
     * @param \DateTime $date
     * @return UserInput
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return UserInput
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
     * Add input_entries
     *
     * @param \SE\InputBundle\Entity\InputEntry $inputEntries
     * @return UserInput
     */
    public function addInputEntry(\SE\InputBundle\Entity\InputEntry $inputEntry)
    {
        $this->input_entries[] = $inputEntry;
        $inputEntry->setUserInput($this);
        return $this;
    }

    /**
     * Remove input_entries
     *
     * @param \SE\InputBundle\Entity\InputEntry $inputEntries
     */
    public function removeInputEntry(\SE\InputBundle\Entity\InputEntry $inputEntry)
    {
        $this->input_entries->removeElement($inputEntry);
        //$inputEntry->setUserInput(null);
    }

    /**
     * Get input_entries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInputEntries()
    {
        return $this->input_entries;
    }

    /**
     * Set dateInput
     *
     * @param \DateTime $dateInput
     * @return UserInput
     */
    public function setDateInput($dateInput)
    {
        $this->dateInput = $dateInput;

        return $this;
    }

    /**
     * Get dateInput
     *
     * @return \DateTime 
     */
    public function getDateInput()
    {
        return $this->dateInput;
    }

    /**
     * Set team
     *
     * @param \SE\InputBundle\Entity\Team $team
     * @return UserInput
     */
    public function setTeam(\SE\InputBundle\Entity\Team $team)
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
     * @return UserInput
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
     * Set totalHoursInput
     *
     * @param integer $totalHoursInput
     * @return UserInput
     */
    public function setTotalHoursInput($totalHoursInput)
    {
        $this->totalHoursInput = $totalHoursInput;

        return $this;
    }

    /**
     * Get totalHoursInput
     *
     * @return integer 
     */
    public function getTotalHoursInput()
    {
        return $this->totalHoursInput;
    }

    /**
     * Set totalWorkingHoursInput
     *
     * @param integer $totalWorkingHoursInput
     * @return UserInput
     */
    public function setTotalWorkingHoursInput($totalWorkingHoursInput)
    {
        $this->totalWorkingHoursInput = $totalWorkingHoursInput;

        return $this;
    }

    /**
     * Get totalWorkingHoursInput
     *
     * @return integer 
     */
    public function getTotalWorkingHoursInput()
    {
        return $this->totalWorkingHoursInput;
    }

    /**
     * Set totalOvertimeInput
     *
     * @param string $totalOvertimeInput
     * @return UserInput
     */
    public function setTotalOvertimeInput($totalOvertimeInput)
    {
        $this->totalOvertimeInput = $totalOvertimeInput;

        return $this;
    }

    /**
     * Get totalOvertimeInput
     *
     * @return string 
     */
    public function getTotalOvertimeInput()
    {
        return $this->totalOvertimeInput;
    }

    /**
     * Set totalToInput
     *
     * @param integer $totalToInput
     * @return UserInput
     */
    public function setTotalToInput($totalToInput)
    {
        $this->totalToInput = $totalToInput;

        return $this;
    }

    /**
     * Get totalToInput
     *
     * @return integer 
     */
    public function getTotalToInput()
    {
        return $this->totalToInput;
    }

    /**
     * Set totalProdInput
     *
     * @param string $totalProdInput
     * @return UserInput
     */
    public function setTotalProdInput($totalProdInput)
    {
        $this->totalProdInput = $totalProdInput;

        return $this;
    }

    /**
     * Get totalProdInput
     *
     * @return string 
     */
    public function getTotalProdInput()
    {
        return $this->totalProdInput;
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function computeHours(){

        $totHI = 0;
        $totWHI = 0;
        $totOI = 0;
        $totTO = 0;

        foreach ($this->getInputEntries() as $i) {
            
            $totH = 0;
            $totWH = 0;
            $totO = 0;
            $prodH = 0;


            foreach ($i->getActivityHours() as $a) {
                
                $totH += $a->getRegularHours();
                $totO += $a->getOtHours();
                if ($a->getActivity()->getProductive()){
                    $totWH += $a->getRegularHours() + $a->getOtHours();
                }
                if ($a->getActivity()->getTrackable() and $a->getActivity()->getProductive()){
                    $prodH += $a->getRegularHours() + $a->getOtHours();
                }
            }

            //pour input entry
            $i->setTotalHours($totH + $totO);
            $i->setTotalWorkingHours($totWH);
            $i->setTotalOvertime($totO);

            if( $i->getTotalTo() > 0 and $prodH > 0){
                $i->setTotalProd($i->getTotalTo() / $prodH);
            }

            //pour user input
            $totHI += $totH + $totO;
            $totWHI += $totWH;
            $totOI += $totO;
            $totTO += $i->getTotalTo();
        }

        $this->totalHoursInput = $totHI;
        $this->totalWorkingHoursInput = $totWHI;
        $this->totalOvertimeInput = $totOI;
        $this->totalToInput = $totTO;
    
        if( $this->totalToInput > 0 and $this->totalWorkingHoursInput > 0){
            $this->totalProdInput = $this->totalToInput / $this->totalWorkingHoursInput;
        }
    }


    /**
     * Set process
     *
     * @param boolean $process
     * @return UserInput
     */
    public function setProcess($process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Get process
     *
     * @return boolean 
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * Set review
     *
     * @param boolean $review
     * @return UserInput
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return boolean 
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set user
     *
     * @param \SE\InputBundle\Entity\User $user
     * @return UserInput
     */
    public function setUser(\SE\InputBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \SE\InputBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
