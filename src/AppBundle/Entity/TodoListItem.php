<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Mapping\EntityBase;

/**
 * Class TodoListItem
 * @package AppBundle\Entity
 * 
 * @ORM\Entity
 * @ORM\Table(name="list_items")
 * @ORM\HasLifecycleCallbacks
 */
class TodoListItem extends EntityBase
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $description;
	
	/**
	 * @ORM\Column(name="is_completed", type="boolean")
	 */
	protected $isCompleted = 0;
	
	/**
     * @ORM\ManyToOne(targetEntity="TodoList", inversedBy="items")
     * @ORM\JoinColumn(name="list_id", referencedColumnName="id")
	 */
	protected $list;
	
	/**
	 * @return mixed
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * @return mixed
	 */
	public function getDescription(){
		return $this->description;
	}
	
	/**
	 * @param mixed $description
	 * @return TodoListItem
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		
		return $this;
	}
	
	/**
	 * @param mixed $isCompleted
	 * @return TodoListItem
	 */
	public function setIsCompleted($isCompleted)
	{
		$this->isCompleted = $isCompleted;
		
		return $this;
	}
	
	/**
	 * @param mixed $list
	 * @return TodoListItem
	 */
	public function setList($list)
	{
		$this->list = $list;
		
		return $this;
	}
}