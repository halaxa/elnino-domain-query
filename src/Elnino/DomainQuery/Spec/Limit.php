<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 14.2.14
 * Time: 9:59
 */

namespace Elnino\DomainQuery\Spec;


use Elnino\DomainQuery\QueryModifierInterface;
use Doctrine\ORM\Query;

/**
 * Limit spec for simple limit only. eg: new Limit (5)
 */
class Limit implements QueryModifierInterface
{
    /** @var  int */
    private $limit;

    /**
     * @param int $limit
     */
    public function __construct($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Modifies parameters of a query object.
     *
     * @param Query $query
     * @return void
     */
    public function modifyQuery(Query $query)
    {
        $query->setMaxResults($this->limit);
    }
}
