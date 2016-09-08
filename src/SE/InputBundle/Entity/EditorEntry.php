<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EditorEntry
 *
 * @ORM\Table(name="editorentry")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\EditorEntryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EditorEntry
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
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", length=255, nullable=true)
     */
    private $updatedAt;

    /**
     * @Assert\NotBlank(message = "Choose your name in the user list.")
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Assert\NotBlank(message = "Choose an employee for this edition, it won't work so good otherwise.")
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\Employee", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $employee;

    /**
     * @Assert\NotBlank(message = "Hey! No SESA = No Lines, No Lines = Bad Productivity ! No good lah!")
     * @ORM\Column(name="sesa", type="string", length=255, nullable=true)
     */
    private $sesa;

    /**
     * @var boolean
     *
     * @ORM\Column(name="present", type="boolean")
     * @Assert\NotNull()
     */
    private $present;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\AbsenceReason", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $absenceReason;

    /**
     * @ORM\OneToMany(targetEntity="SE\InputBundle\Entity\EditorActivity", mappedBy="editorEntry", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid()
     */
    private $editorActivities;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", length=255, nullable=true)
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\UserInput", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $userInput;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\InputEntry", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $inputEntry;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\EditorStatus", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $editorStatus;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\EditorType", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $editorType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="halfday", type="boolean")
     * @Assert\NotNull()
     */
    private $halfday;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \Datetime();
        $this->editorActivities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return EditorEntry
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return EditorEntry
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
     * Set sesa
     *
     * @param string $sesa
     * @return EditorEntry
     */
    public function setSesa($sesa)
    {
        $this->sesa = $sesa;

        return $this;
    }

    /**
     * Get sesa
     *
     * @return string 
     */
    public function getSesa()
    {
        return $this->sesa;
    }

    /**
     * Set present
     *
     * @param boolean $present
     * @return EditorEntry
     */
    public function setPresent($present)
    {
        $this->present = $present;

        return $this;
    }

    /**
     * Get present
     *
     * @return boolean 
     */
    public function getPresent()
    {
        return $this->present;
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return EditorEntry
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set user
     *
     * @param \SE\InputBundle\Entity\User $user
     * @return EditorEntry
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

    /**
     * Set employee
     *
     * @param \SE\InputBundle\Entity\Employee $employee
     * @return EditorEntry
     */
    public function setEmployee(\SE\InputBundle\Entity\Employee $employee)
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
     * Set absenceReason
     *
     * @param \SE\InputBundle\Entity\AbsenceReason $absenceReason
     * @return EditorEntry
     */
    public function setAbsenceReason(\SE\InputBundle\Entity\AbsenceReason $absenceReason = null)
    {
        $this->absenceReason = $absenceReason;

        return $this;
    }

    /**
     * Get absenceReason
     *
     * @return \SE\InputBundle\Entity\AbsenceReason 
     */
    public function getAbsenceReason()
    {
        return $this->absenceReason;
    }

    /**
     * Add editorActivities
     *
     * @param \SE\InputBundle\Entity\EditorActivity $editorActivities
     * @return EditorEntry
     */
    public function addEditorActivity(\SE\InputBundle\Entity\EditorActivity $editorActivities)
    {
        $this->editorActivities[] = $editorActivities;
        $editorActivities->setEditorEntry($this);

        return $this;
    }

    /**
     * Remove editorActivities
     *
     * @param \SE\InputBundle\Entity\EditorActivity $editorActivities
     */
    public function removeEditorActivity(\SE\InputBundle\Entity\EditorActivity $editorActivities)
    {
        $this->editorActivities->removeElement($editorActivities);
    }

    /**
     * Get editorActivities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEditorActivities()
    {
        return $this->editorActivities;
    }

    /**
     * Set userInput
     *
     * @param \SE\InputBundle\Entity\UserInput $userInput
     * @return EditorEntry
     */
    public function setUserInput(\SE\InputBundle\Entity\UserInput $userInput)
    {
        $this->userInput = $userInput;

        return $this;
    }

    /**
     * Get userInput
     *
     * @return \SE\InputBundle\Entity\UserInput 
     */
    public function getUserInput()
    {
        return $this->userInput;
    }

    /**
     * Set editorStatus
     *
     * @param \SE\InputBundle\Entity\EditorStatus $editorStatus
     * @return EditorEntry
     */
    public function setEditorStatus(\SE\InputBundle\Entity\EditorStatus $editorStatus)
    {
        $this->editorStatus = $editorStatus;

        return $this;
    }

    /**
     * Get editorStatus
     *
     * @return \SE\InputBundle\Entity\EditorStatus 
     */
    public function getEditorStatus()
    {
        return $this->editorStatus;
    }

    /**
     * Set inputEntry
     *
     * @param \SE\InputBundle\Entity\InputEntry $inputEntry
     * @return EditorEntry
     */
    public function setInputEntry(\SE\InputBundle\Entity\InputEntry $inputEntry = null)
    {
        $this->inputEntry = $inputEntry;

        return $this;
    }

    /**
     * Get inputEntry
     *
     * @return \SE\InputBundle\Entity\InputEntry 
     */
    public function getInputEntry()
    {
        return $this->inputEntry;
    }

    /**
     * @Assert\IsTrue(message = "Oooh careful ! You better input a comment for this kind of activity!")
     */
    public function isExcludedActivityCommented()
    {
        foreach ($this->editorActivities as $a){
            $i = $a->getActivity()->getId();
            //COMMENT PROPERTY NEEDED IN ACTIVITIES
            if( ($i == 11 || $i == 7) && $this->comments === null){
                return false;
            }
        }
        return true;
    }

    /**
     * @Assert\IsTrue(message = "Oooh no! I think that more than 11 hours a day is too much for a man... let the man sleep!")
     */
    public function isTooManyHours()
    {   
        $total = 0;
        foreach ($this->editorActivities as $a) {
            $total += $a->getRegularHours() + $a->getOtHours();
        }
        return $total <= 11;
    }

    /**
     * Set editorType
     *
     * @param \SE\InputBundle\Entity\EditorType $editorType
     * @return EditorEntry
     */
    public function setEditorType(\SE\InputBundle\Entity\EditorType $editorType)
    {
        $this->editorType = $editorType;

        return $this;
    }

    /**
     * Get editorType
     *
     * @return \SE\InputBundle\Entity\EditorType 
     */
    public function getEditorType()
    {
        return $this->editorType;
    }

    /**
     * Set halfday
     *
     * @param boolean $halfday
     * @return EditorEntry
     */
    public function setHalfday($halfday)
    {
        $this->halfday = $halfday;

        return $this;
    }

    /**
     * Get halfday
     *
     * @return boolean 
     */
    public function getHalfday()
    {
        return $this->halfday;
    }
}
