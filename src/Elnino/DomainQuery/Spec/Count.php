<?php

namespace Elnino\DomainQuery\Spec;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\QueryBuilder;
use Elnino\DomainQuery\QueryBuilderModifierInterface;
use Elnino\DomainQuery\QueryModifierInterface;

/**
 * Selects count of entities
 *
 * @author Petr Knap <petr.knap@elnino.cz>
 * @since 2015-01-09
 * @package Elnino\DomainQuery\Spec
 */
class Count implements QueryBuilderModifierInterface, QueryModifierInterface
{
    /**
     * Adds to QueryBuilder custom parameters, joins etc.
     *
     * @param QueryBuilder $qb
     * @return void
     */
    public function modifyQueryBuilder(QueryBuilder $qb)
    {
        /** @var From $from */
        $from = $qb->getDQLPart("from")[0];
        $alias = $from->getAlias();
        $qb->select("count({$alias})");
    }

    /**
     * Modifies parameters of a query object.
     *
     * @param Query $query
     * @return void
     */
    public function modifyQuery(Query $query)
    {
//        $query->setHydrationMode(Query::HYDRATE_SINGLE_SCALAR);
    }
}
