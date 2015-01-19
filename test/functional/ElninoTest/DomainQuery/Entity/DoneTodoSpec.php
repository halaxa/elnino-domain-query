<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 12.2.14
 * Time: 12:57
 */

namespace ElninoTest\DomainQuery\Entity;


use Elnino\DomainQuery\SpecExpr as Expression;
use Elnino\DomainQuery\SpecInterface;
use Doctrine\ORM\Query\Expr;

class DoneTodoSpec implements SpecInterface
{
    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return Expression
     */
    public function expression($alias = null)
    {
        return new Expression(
            (new Expr)->eq("$alias.done", ':done'),
            [':done' => 1]
        );
    }
}
