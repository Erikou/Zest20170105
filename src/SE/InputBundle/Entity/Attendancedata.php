<?php
namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attendancedata
 *
 * @ORM\Table(name="Attendancedata")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\AttendancedataRepository")
 */
class Attendancedata
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
	 * @ORM\Column(name="updatedAt", type="datetime")
	 */
	private $updatedAt;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="refresher", type="integer")
	 */
	private $refresher;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="year", type="integer")
	 */
	private $year;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="month", type="integer")
	 */
	private $month;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="jsonAttendance", type="string")
	 */
	private $jsonAttendance;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="jsonData", type="string")
	 */
	private $jsonData;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tableTemplate", type="string")
	 */
	private $tableTemplate;

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
	 * Constructor
	 */
	public function __construct()
	{
		$this->date_creation = new \Datetime("now");
	}

	/**
	 * Set createdAt
	 *
	 * @param \DateTime $createdAt
	 * @return Attendancedata
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
	 * @return Attendancedata
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
	 * Set refresher
	 *
	 * @param boolean $refresher
	 * @return Attendancedata
	 */
	public function setRefresher($refresher)
	{
		$this->refresher = $refresher;
	
		return $this;
	}
	
	/**
	 * Get refresher
	 *
	 * @return boolean
	 */
	public function getRefresher()
	{
		return $this->refresher;
	}

	/**
	 * Set year
	 *
	 * @param integer $year
	 * @return Attendancedata
	 */
	public function setYear($year)
	{
		$this->year = $year;
	
		return $this;
	}
	
	/**
	 * Get year
	 *
	 * @return integer
	 */
	public function getYear()
	{
		return $this->year;
	}

	/**
	 * Set month
	 *
	 * @param integer $month
	 * @return Attendancedata
	 */
	public function setMonth($month)
	{
		$this->month = $month;
	
		return $this;
	}
	
	/**
	 * Get month
	 *
	 * @return integer
	 */
	public function getMonth()
	{
		return $this->month;
	}
}
