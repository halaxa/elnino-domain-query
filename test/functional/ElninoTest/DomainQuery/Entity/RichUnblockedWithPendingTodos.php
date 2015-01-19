<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 25.2.14
 * Time: 9:37
 */

namespace ElninoTest\DomainQuery\Entity;


use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\JoinExpr;
use Elnino\DomainQuery\Spec\AndX;
use Elnino\DomainQuery\Spec\Join;
use Elnino\DomainQuery\Spec\NotX;
use Elnino\DomainQuery\SpecInterface;

class RichUnblockedWithPendingTodos implements SpecInterface
{
    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return SpecExpr
     */
    public function expression($alias = null)
    {
        return (new AndX(
            (new RichUnblockedSpec())->expression($alias),
            new Join (
                new JoinExpr(JoinExpr::INNER_JOIN, "$alias.todos", 'todo'),
                (new NotX((new DoneTodoSpec())->expression('todo')))->expression()
            )
        ))->expression($alias);
    }
}
