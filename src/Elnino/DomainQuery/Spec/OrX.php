<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 21.2.14
 * Time: 14:18
 */

namespace Elnino\DomainQuery\Spec;

/**
 * OR operator operating either on \Doctrine\ORM\Query\Expr\* or ExpressionSpecificationInterface
 */
class OrX extends AbstractOperator
{
    /**
     * @param array $operands Doctrine \Doctrine\ORM\Query\Expr\* operands on which the operand should be applied
     * @return \Doctrine\ORM\Query\Expr\* Result of the operand
     */
    public function doTheMath(array $operands)
    {
        return new \Doctrine\ORM\Query\Expr\Orx($operands);
    }

} 
