<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 28.2.14
 * Time: 10:21
 */

namespace ElninoTest\DomainQuery\Entity;


use Elnino\DomainQuery\AliasAwareSpecInterface;
use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\SpecInterface;

class WellRatedSpec implements SpecInterface
{
    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return SpecExpr
     */
    public function expression($alias = null)
    {
        $e = new \Doctrine\ORM\Query\Expr();
        return new SpecExpr(
            $e->gt("$alias.stars", ':stars'),
            [':stars' => 3]
        );
    }

} 
