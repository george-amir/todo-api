<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Mapping\EntityBase;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class TodoList
 * @package AppBundle\Entity
 * 
 * @ORM\Entity
 * @ORM\Table(name="lists")
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("all")
 */
class TodoList extends EntityBase
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @Expose
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string")
	 * @Expose
	 */
	protected $title;
	
	/**
     * @ORM\OneToMany(targetEntity="TodoListItem", mappedBy="list", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Expose
	 */
    private $items;
	
	/**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="lists")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
	
	/**
	 * @return mixed
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * @return mixed
	 */
	public function getTitle(){
		return $this->title;
	}
	
	/**
	 * @return mixed
	 */
	public function getItems()
	{
		return $this->items;
	}
	
	/**
	 * @return mixed
	 */
	public function getOwner()
	{
		return $this->user;
	}
	
	/**
	 * @param mixed $title
	 * @return TodoList
	 */
	public function setOwner($user)
	{
		$this->user = $user;
		
		return $this;
	}
	
	/**
	 * @param mixed $title
	 * @return TodoList
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		
		return $this;
	}
	
	public function removeItems($item)
    {
        $item->setList(null);
		$this->items->remove($item);
    }
	
	public function __construct() {
        $this->items = new ArrayCollection();
    }
}