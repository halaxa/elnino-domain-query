<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 21.2.14
 * Time: 14:03
 */

namespace Elnino\DomainQuery\Spec;


use Elnino\DomainQuery\DefaultSpecificationRepository;
use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\SpecInterface;

/**
 * Ancestor of ExprInterface operators
 */
abstract class AbstractOperator implements SpecInterface
{
    /** @var  SpecExpr[] */
    private $args;

    /**
     * @param array $operands Doctrine \Doctrine\ORM\Query\Expr\* operands on which the operator should be applied
     * @return \Doctrine\ORM\Query\Expr\* Result of the operand
     */
    abstract public function doTheMath(array $operands);

    /**
     * @param SpecExpr|SpecInterface $param One or more parameters
     * @throws \InvalidArgumentException
     */
    public function __construct($param)
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            if ( ! ($arg instanceof SpecExpr || $arg instanceof SpecInterface)) {
                throw new \InvalidArgumentException(sprintf(
                    'Argument passed to %s::__construct() must be either %s or %s. Given %s.',
                    static::class,
                    SpecExpr::class,
                    SpecInterface::class,
                    is_object($arg) ? get_class($arg) : gettype($arg)
                ));
            }
        }

        $this->args = $args;
    }

    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return SpecExpr
     */
    public function expression($alias = null)
    {
        $exprParams = $binds = $joins = [];
        $expr = null;

        foreach ($this->args as $arg) {
            if ($arg instanceof SpecInterface) {
                $arg = DefaultSpecificationRepository::getExprFromSpec($arg, $alias);
            }
            if ($arg instanceof SpecExpr) {
                $exprParams[] = $arg->getExpression();
                $binds = array_merge($binds, $arg->getBinds());
                $joins = array_merge($joins, $arg->getJoins());
            }
        }

        return new SpecExpr($this->doTheMath($exprParams), $binds, $joins);
    }
} 
