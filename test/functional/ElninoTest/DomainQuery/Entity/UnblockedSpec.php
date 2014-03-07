<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 26.2.14
 * Time: 10:42
 */

namespace ElninoTest\DomainQuery\Entity;


use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\SpecInterface;

class UnblockedSpec implements SpecInterface
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
            $e->eq("$alias.blocked", ':blocked'),
            [':blocked' => 0]
        );
    }
} 
