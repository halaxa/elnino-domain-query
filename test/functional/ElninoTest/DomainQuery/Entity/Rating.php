<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 29.1.14
 * Time: 12:36
 */

namespace ElninoTest\DomainQuery\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Rating
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * Num of stars 0-5
     *
     * @ORM\Column(type="integer")
     */
    private $stars;

    /**
     * @ORM\Column
     */
    private $comment;
}
