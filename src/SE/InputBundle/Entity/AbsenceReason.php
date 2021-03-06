<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AbsenceReason
 *
 * @ORM\Table(name="absencereason")
 * @ORM\Entity
 */
class AbsenceReason
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
     * @var string
     *
     * @ORM\Column(name="detail", type="string", length=255)
     */
    private $detail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="justified", type="boolean")
     */
    private $justified;


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
     * @return AbsenceReason
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
     * Set justified
     *
     * @param boolean $justified
     * @return AbsenceReason
     */
    public function setJustified($justified)
    {
        $this->justified = $justified;

        return $this;
    }

    /**
     * Get justified
     *
     * @return boolean 
     */
    public function getJustified()
    {
        return $this->justified;
    }

    /**
     * Set detail
     *
     * @param string $detail
     * @return AbsenceReason
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return string 
     */
    public function getDetail()
    {
        return $this->detail;
    }

    public function getAbsenceSelect()
    {
        return sprintf('%s - %s', $this->name, $this->detail);
    }
}
