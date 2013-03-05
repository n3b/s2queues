<?php

namespace n3b\Queues\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="n3bQueueEntity", indexes={@ORM\Index(name="queue_name", columns={"name"})}))
 * @ORM\Entity(repositoryClass="n3b\Queues\Entity\QueueRepository")
 */
class QueueMessage
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=36)
	 * @ORM\GeneratedValue(strategy="UUID")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * @ORM\Column(type="object")
	 */
	protected $data;

    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Queue
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
     * Set data
     *
     * @param \stdClass $data
     * @return Queue
     */
    public function setData($data)
    {
        $this->data = $data;
    
        return $this;
    }

    /**
     * Get data
     *
     * @return \stdClass 
     */
    public function getData()
    {
        return $this->data;
    }
}