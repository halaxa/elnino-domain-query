<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 3.3.14
 * Time: 8:29
 */

namespace ElninoTest\DomainQuery\Entity;


use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\QueryModifierInterface;
use Elnino\DomainQuery\SpecInterface;
use Doctrine\ORM\Query;

class MasterUnblockedSpec implements SpecInterface, QueryModifierInterface
{
    /**
     * Modifies parameters of a query object.
     *
     * @param Query $query
     * @return void
     */
    public function modifyQuery(Query $query)
    {
        $query->setMaxResults(1);
    }

    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return SpecExpr
     */
    public function expression($alias = null)
    {
        return new UnblockedSpec();
    }

} 
