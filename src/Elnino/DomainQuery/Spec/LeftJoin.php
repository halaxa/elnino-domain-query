<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 3.3.14
 * Time: 7:52
 */

namespace Elnino\DomainQuery\Spec;


use Elnino\DomainQuery\JoinExpr;

/**
 * Spec used to join other specs even other LeftJoins or Joins
 */
class LeftJoin extends Join
{
    const JOIN_TYPE = JoinExpr::LEFT_JOIN;
} 
