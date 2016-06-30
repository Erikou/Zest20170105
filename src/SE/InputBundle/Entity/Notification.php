<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="SE\InputBundle\Entity\NotificationRepository")
 */
class Notification
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
    private $receiver;

    /**
     * @ORM\ManyToOne(targetEntity="SE\InputBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_been_read", type="boolean")
     */
    private $hasBeenRead;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="date_creation", type="datetime", nullable=false)
     */
    private $dateCreation;

    public function __construct()
    {
        $this->dateCreation = new \Datetime();
        $this->hasBeenRead = false;
        $this->receiver = null;
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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Notification
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }
    public function getDateCreationString()
    {
    	$this->dateCreation->setTimezone(new \DateTimeZone(date_default_timezone_get()));
    	$this->dateCreation->setTimezone(new \DateTimeZone("Asia/Shanghai"));
        return $this->dateCreation->format('d/m/Y H:i');
    }

    /**
     * Set receiver
     *
     * @param \SE\InputBundle\Entity\User $receiver
     * @return Notification
     */
    public function setReceiver(\SE\InputBundle\Entity\User $receiver = null)
    {
    	if ($this->receiver != null)
    		$this->receiver->setUnreadNotifications($this->receiver->getUnreadNotifications() - 1);
        $this->receiver = $receiver;
    	$this->receiver->setUnreadNotifications($this->receiver->getUnreadNotifications() + 1);

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \SE\InputBundle\Entity\User
     */
    public function getReceiver()
    {
        return $this->receiver;
    }
    
    /**
     * Set sender
     *
     * @param \SE\InputBundle\Entity\User $sender
     * @return Notification
     */
    public function setSender(\SE\InputBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \SE\InputBundle\Entity\User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set hasBeenRead
     *
     * @param boolean $hasBeenRead
     * @return Notification
     */
    public function setHasBeenRead($hasBeenRead)
    {
    	if ($hasBeenRead && !$this->hasBeenRead)
    		$this->receiver->setUnreadNotifications($this->receiver->getUnreadNotifications() - 1);
    	else if (!$hasBeenRead && $this->hasBeenRead)
    		$this->receiver->setUnreadNotifications($this->receiver->getUnreadNotifications() + 1);
        $this->hasBeenRead = $hasBeenRead;

        return $this;
    }

    /**
     * Get hasBeenRead
     *
     * @return boolean 
     */
    public function getHasBeenRead()
    {
        return $this->hasBeenRead;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Notification
     */
    public function setTitle($title)
    {
    	$this->title = $title;
    
    	return $this;
    }
    
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
    	return $this->title;
    }
    
    /**
     * Set text
     *
     * @param string $text
     * @return Notification
     */
    public function setText($text)
    {
    	$this->text = $text;
    
    	return $this;
    }
    
    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
    	return $this->text;
    }
    public function getMessagePreview(){
    	$sub = substr($this->text, 0, 128 - strlen($this->title));
    	if (strlen($sub) < strlen($this->text))
    		$sub = $sub."...";
    	return $sub;
    }
    
}
