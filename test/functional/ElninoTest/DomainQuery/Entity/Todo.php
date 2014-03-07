<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 7.2.14
 * Time: 15:06
 */

namespace ElninoTest\DomainQuery\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Todo
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
     * @ORM\ManyToOne(targetEntity="Person")
     */
    private $person;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $done;

    function __construct()
    {
        $this->ratings = new ArrayCollection();
    }


} 
