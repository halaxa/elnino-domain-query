<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 28.2.14
 * Time: 8:07
 */

namespace ElninoTest\DomainQuery\Entity;


use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\SpecInterface;

class RecursiveSpec implements SpecInterface
{
    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return SpecExpr
     */
    public function expression($alias = null)
    {
        return $this;
    }

} 
