<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 7.2.14
 * Time: 14:54
 */

namespace ElninoTest\DomainQuery\Entity;


use Elnino\DomainQuery\SpecExpr as Expression;
use Elnino\DomainQuery\SpecInterface;
use Doctrine\ORM\Query\Expr;

class RichUnblockedSpec implements SpecInterface
{
    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return Expr
     */
    public function expression($alias = null)
    {
        $e = new Expr();

        return new Expression(
            $e->andX(
                $e->eq ("$alias.blocked", ':blocked'),
                $e->gte("$alias.salary",  ':salary')
            ),
            [':blocked' => 0, ':salary' => 5000]
        );
    }
}
