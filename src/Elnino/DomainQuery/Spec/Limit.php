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
 * Limit spec for limit with offset. eg: new Limit(5)
 */
class Limit implements QueryModifierInterface
{
    /** @var  int */
    private $limit;

    /** @var  int */
    private $offset;

    /**
     * @param int $limit
     * @param int $offset
     */
    public function __construct($limit, $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * Modifies parameters of a query object.
     *
     * @param Query $query
     * @return void
     */
    public function modifyQuery(Query $query)
    {
        $query->setFirstResult($this->offset);
        $query->setMaxResults($this->limit);
    }
}
