<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 21.2.14
 * Time: 14:18
 */

namespace Elnino\DomainQuery\Spec;
use Doctrine\ORM\Query\Expr;

/**
 * NOT operator operating either on \Doctrine\ORM\Query\Expr\* or ExpressionSpecificationInterface
 */
class NotX extends AbstractOperator
{
    /**
     * @param array $operands Doctrine \Doctrine\ORM\Query\Expr\* operands on which the operand should be applied
     * @return \Doctrine\ORM\Query\Expr\* Result of the operand
     */
    public function doTheMath(array $operands)
    {
        return (new Expr())->not($operands[0]);
    }

} 
