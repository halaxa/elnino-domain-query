<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 21.2.14
 * Time: 9:02
 */

namespace Elnino\DomainQuery;

/**
 * Class SpecificationExpression
 */
class SpecExpr
{
    /** @var  \Doctrine\ORM\Query\Expr\* */
    private $expression;

    /** @var  array Array of [':param' => 'Value'] */
    private $binds;

    /** @var  JoinExpr[] */
    private $joins;

    /**
     * @param \Doctrine\ORM\Query\Expr\* $expression
     * @param array                      $binds
     * @param JoinExpr|JoinExpr[]        $joins JoinExpr or array of them
     */
    public function __construct($expression = null, $binds = [], $joins = [])
    {
        $this->expression = $expression;
        $this->binds = (array) $binds;
        $this->joins = (array) $joins;
    }

    /**
     * @return array
     */
    public function getBinds()
    {
        return $this->binds;
    }

    /**
     * @return \Doctrine\ORM\Query\Expr\*
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @return \Elnino\DomainQuery\JoinExpr[]
     */
    public function getJoins()
    {
        return $this->joins;
    }
}
