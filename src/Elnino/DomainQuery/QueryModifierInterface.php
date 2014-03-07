<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 12.2.14
 * Time: 15:46
 */

namespace Elnino\DomainQuery;


use Doctrine\ORM\Query;

interface QueryModifierInterface
{
    /**
     * Modifies parameters of a query object.
     *
     * @param Query $query
     * @return void
     */
    public function modifyQuery(Query $query);
}
