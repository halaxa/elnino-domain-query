<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 31.1.14
 * Time: 13:54
 */

namespace Elnino\DomainQuery;


use Doctrine\ORM\QueryBuilder;

interface QueryBuilderModifierInterface
{
    /**
     * Adds to QueryBuilder custom parameters, joins etc.
     *
     * @param QueryBuilder $qb
     * @return void
     */
    public function modifyQueryBuilder(QueryBuilder $qb);
}
