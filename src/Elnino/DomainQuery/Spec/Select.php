<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 8.1.2015
 * Time: 15:50
 */

namespace Elnino\DomainQuery\Spec;


use Doctrine\ORM\QueryBuilder;
use Elnino\DomainQuery\QueryBuilderModifierInterface;

class Select implements QueryBuilderModifierInterface
{
    /** @var string  */
    private $select = "";

    /**
     * @param string $select Select clause
     */
    function __construct($select)
    {
        $this->select = $select;
    }

    /**
     * Adds to QueryBuilder custom parameters, joins etc.
     *
     * @param QueryBuilder $qb
     * @return void
     */
    public function modifyQueryBuilder(QueryBuilder $qb)
    {
        $qb->select($this->select);
    }

}
