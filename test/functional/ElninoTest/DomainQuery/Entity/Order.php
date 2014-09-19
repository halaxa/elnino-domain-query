<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 19.9.14
 * Time: 9:21
 */

namespace ElninoTest\DomainQuery\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="prefix_order")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;
}
