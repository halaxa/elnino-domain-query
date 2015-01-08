<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 8.1.2015
 * Time: 15:53
 */

namespace Elnino\DomainQuery\Spec;


use Doctrine\ORM\Query;
use Elnino\DomainQuery\QueryModifierInterface;

class Hydrate implements QueryModifierInterface
{
    /** @var  int */
    private $hydrationMode = Query::HYDRATE_OBJECT;

    /**
     * @param int $hydrateMode One of Query::HYDRATE_* constants
     */
    function __construct($hydrateMode)
    {
        $this->hydrationMode = $hydrateMode;
    }


    /**
     * Modifies parameters of a query object.
     *
     * @param Query $query
     * @return void
     */
    public function modifyQuery(Query $query)
    {
        $query->setHydrationMode($this->hydrationMode);
    }
}
