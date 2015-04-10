<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 25.2.14
 * Time: 11:09
 */

namespace Elnino\DomainQuery\Spec;


use Elnino\DomainQuery\DefaultSpecificationRepository;
use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\JoinExpr;
use Elnino\DomainQuery\SpecInterface;

/**
 * Spec used to join other specs even other Joins or LeftJoins
 */
class Join implements SpecInterface
{
    const JOIN_TYPE = JoinExpr::INNER_JOIN;

    static private $counter = 0;

    /** @var  JoinExpr */
    private $join;

    /** @var  SpecExpr */
    private $expr;

    /**
     * @param JoinExpr|string              $join
     * @param SpecExpr|SpecInterface $expr
     * @throws \InvalidArgumentException
     */
    public function __construct($join, $expr = null)
    {
        $expr = $expr ?: new SpecExpr();
        $join = $join instanceof JoinExpr ? $join : JoinExpr::fromString(static::JOIN_TYPE, $join);
        if ( ! ($expr instanceof SpecInterface || $expr instanceof SpecExpr)) {
            throw new \InvalidArgumentException(sprintf(
                '%s::__construct() only accepts %s or %s. Given %s',
                self::class,
                SpecInterface::class,
                SpecExpr::class,
                is_object($expr) ? get_class($expr) : gettype($expr)
            ));
        }
        $this->join = $join;
        $this->expr = $expr;
    }

    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return SpecExpr
     */
    public function expression($alias = null)
    {
        $joinAlias = $this->getJoinAlias($alias);

        if ($this->expr instanceof SpecInterface) {
            $this->expr = DefaultSpecificationRepository::getExprFromSpec($this->expr, $joinAlias);
        }

        $j = $this->join;
        if (class_exists($j->getJoin())) { // Is it arbitrary join?
            $join = $j->getJoin();
        } elseif (strpos($j->getJoin(), ".")) { // Is alias provided?
            $join = $j->getJoin();
        } else {
            $join = $alias . "." . $j->getJoin();
        }
        $joinAlias = $joinAlias ?: $j->getAlias();

        return new SpecExpr(
            $this->expr->getExpression(),
            $this->expr->getBinds(),
            array_merge(
                [new JoinExpr(
                    $j->getJoinType(), $join, $joinAlias, $j->getConditionType(), $j->getCondition(), $j->getIndexBy()
                )],
                $this->expr->getJoins()
            )
        );
    }

    /**
     * @param string $alias
     * @return string
     */
    private function getJoinAlias($alias)
    {
        return $this->join->getAlias()
            ?: $alias.array_reverse(explode('.', $this->join->getJoin()))[0].'_'; // eliminates reserved words collision
    }
}
