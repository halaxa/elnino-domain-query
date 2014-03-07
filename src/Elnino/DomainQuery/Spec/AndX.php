<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 21.2.14
 * Time: 11:25
 */

namespace Elnino\DomainQuery\Spec;


/**
 * AND operator operating either on \Doctrine\ORM\Query\Expr\* or ExpressionSpecificationInterface
 */
class AndX extends AbstractOperator
{
    /**
     * @param array $operands Doctrine \Doctrine\ORM\Query\Expr\* operands on which the operand should be applied
     * @return \Doctrine\ORM\Query\Expr\* Result of the operand
     */
    public function doTheMath(array $operands)
    {
        return new \Doctrine\ORM\Query\Expr\Andx($operands);
    }

}
