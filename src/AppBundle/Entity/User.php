<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

	/**
     * @ORM\OneToMany(targetEntity="TodoList", mappedBy="user")
     */
    private $lists;
	
	public function getLists(){
		return $this->lists;
	}
	
    public function __construct()
    {
        $this->lists = new ArrayCollection();
		parent::__construct();
        // your own logic
    }
}