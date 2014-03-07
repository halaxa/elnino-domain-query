<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 29.1.14
 * Time: 12:36
 */

namespace ElninoTest\DomainQuery\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Rating")
     */
    private $ratings;

    /**
     * @ORM\Column(type="integer")
     */
    private $salary;

    /**
     * @ORM\Column(type="boolean")
     */
    private $blocked;

    /**
     * @ORM\OneToMany(targetEntity="Todo", mappedBy="person")
     */
    private $todos;

    function __construct()
    {
        $this->ratings = $this->todos = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTodos()
    {
        return $this->todos;
    }
}
