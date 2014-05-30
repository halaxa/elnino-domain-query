<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 30.5.14
 * Time: 15:51
 */

namespace Commons\Spec;


use Doctrine\ORM\Query;
use Elnino\DomainQuery\ResultFetcherInterface;

class OneOrNull implements ResultFetcherInterface
{
    /**
     * Returns configured callback which will be used to fetch results from database
     *
     * @param Query $query
     * @return mixed
     */
    public function fetchResult(Query $query)
    {
        $query->getOneOrNullResult();
    }
}
