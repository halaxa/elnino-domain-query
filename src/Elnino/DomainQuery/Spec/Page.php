<?php
/**
 * Created by PhpStorm.
 * User: zaza
 * Date: 12.3.15
 * Time: 9:04
 */

namespace Elnino\DomainQuery\Spec;


use Doctrine\ORM\QueryBuilder;
use Elnino\DomainQuery\QueryBuilderModifierInterface;
use Elnino\DomainQuery\SpecInterface;

class Page implements SpecInterface, QueryBuilderModifierInterface
{


    /**
     * @var int
     */
    private $offset = 0;


    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    const LIMIT_DEFAULT = 25;


    /**
     * @param int $offset
     * @param int $limit
     */
    public function __construct($offset = 0, $limit = self::LIMIT_DEFAULT)
    {

        $this->limit = (int)$limit;
        $this->offset = (int)$offset;

    }

    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return SpecExpr|SpecInterface
     */
    public function expression($alias = null)
    {
    }


    /**
     * Adds to QueryBuilder custom parameters, joins etc.
     *
     * @param QueryBuilder $qb
     * @return void
     */
    public function modifyQueryBuilder(QueryBuilder $qb)
    {

        $qb->setFirstResult($this->offset)->setMaxResults($this->limit);
    }

}