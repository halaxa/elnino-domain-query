<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 26.2.14
 * Time: 10:50
 */

namespace ElninoTest\DomainQuery\Entity;


use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\SpecInterface;

class RichSpec implements SpecInterface
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
            $e->gte("$alias.salary", ':salary'),
            [':salary' => 5000]
        );

    }

}
