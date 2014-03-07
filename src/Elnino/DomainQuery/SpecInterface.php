<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 31.1.14
 * Time: 13:46
 */

namespace Elnino\DomainQuery;


/**
 * Interface for domain logic based, semantic specifications
 */
interface SpecInterface
{
    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return SpecExpr
     */
    public function expression($alias = null);
} 
