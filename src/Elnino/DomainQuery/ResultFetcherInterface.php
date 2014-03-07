<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 24.2.14
 * Time: 8:10
 */

namespace Elnino\DomainQuery;


use Doctrine\ORM\Query;

interface ResultFetcherInterface
{
    /**
     * Returns configured callback which will be used to fetch results from database
     *
     * @param Query $query
     * @return callable
     */
    public function fetchResult(Query $query);
}
