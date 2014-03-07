<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 5.2.14
 * Time: 16:11
 */

namespace Elnino\DomainQuery;
use Doctrine\ORM\Query\Expr\Join;

/**
 * This class is used in specs (mostly SpecInterface) to express a join which the spec needs.
 */
class JoinExpr extends Join
{
    /**
     * @return bool
     */
    public function isLeft()
    {
        return strtoupper($this->joinType) === self::LEFT_JOIN;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return [
            'joinType' => $this->joinType,
            'join' => $this->join,
            'alias' => $this->alias,
            'conditionType' => $this->conditionType,
            'condition' => $this->condition,
            'indexBy' => $this->indexBy,
        ];
    }

    /**
     * @param string $joinType JoinExpr::INNER_JOIN or LEFT_JOIN
     * @param string $joinStr
     * @return self
     */
    static public function fromString($joinType, $joinStr)
    {
        list($join, $alias) = array_merge(array_filter(explode(' ', $joinStr)), [null, null]);
        return new self($joinType, $join, $alias);
    }
}
