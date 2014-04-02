<?php
/**
 * Created by PhpStorm.
 * User: maca
 * Date: 01.04.14
 * Time: 20:20
 */

namespace ElninoTest\DomainQuery\Entity;


use Doctrine\ORM\Query;
use Elnino\DomainQuery\ResultFetcherInterface;

class ArrayResultFetcher implements ResultFetcherInterface {

    /**
     * Returns configured callback which will be used to fetch results from database
     *
     * @param Query $query
     * @return callable
     */
    public function fetchResult(Query $query)
    {
        return $query->getArrayResult();
    }
}
