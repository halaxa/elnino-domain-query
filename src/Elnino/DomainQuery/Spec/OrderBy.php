<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 9.6.14
 * Time: 11:03
 */

namespace Elnino\DomainQuery\Spec;


use Doctrine\ORM\QueryBuilder;
use Elnino\DomainQuery\QueryBuilderModifierInterface;

class OrderBy implements QueryBuilderModifierInterface
{
    /** @var  array */
    private $orderBy;

    /**
     * @param string $orderBy Arguments will be passed to $qb->orderBy()
     */
    function __construct($orderBy)
    {
        $this->orderBy = func_get_args();
    }


    /**
     * Adds to QueryBuilder custom parameters, joins etc.
     *
     * @param QueryBuilder $qb
     * @return void
     */
    public function modifyQueryBuilder(QueryBuilder $qb)
    {
        call_user_func_array([$qb, 'orderBy'], $this->orderBy);
    }

}
