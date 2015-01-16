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
     * Returns end results from database
     *
     * @param Query $query
     * @return mixed
     */
    public function fetchResult(Query $query);
}
