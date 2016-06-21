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
     * @var boolean
     *
     * @ORM\Column(name="validated", type="boolean")
     */
    private $validated;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="date_start", type="date", nullable=false)
     */
    private $date_start;

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
     * Set validated
     *
     * @param boolean $validated
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
     * @return boolean 
     */
    public function getValidated()
    {
        return $this->validated;
    }

}
