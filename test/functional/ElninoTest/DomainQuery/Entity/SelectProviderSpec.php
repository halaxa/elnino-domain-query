<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 3.3.14
 * Time: 11:21
 */

namespace ElninoTest\DomainQuery\Entity;


use Elnino\DomainQuery\QueryBuilderModifierInterface;
use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\Spec\AndX;
use Elnino\DomainQuery\Spec\Join;
use Elnino\DomainQuery\SpecInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class SelectProviderSpec implements SpecInterface, QueryBuilderModifierInterface
{
    private $select;

    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return SpecExpr
     */
    public function expression($alias = null)
    {
        $this->select = "$alias, todo, rating";
        return new AndX(
            new UnblockedSpec(),
            new Join('todos todo',
                new Join('todo.ratings rating',
                    new WellRatedSpec()
                )
            )
        );
    }

    /**
     * Adds to QueryBuilder custom parameters, joins etc.
     *
     * @param QueryBuilder $qb
     * @return void
     */
    public function modifyQueryBuilder(QueryBuilder $qb)
    {
        $qb->select($this->select);
    }


}
