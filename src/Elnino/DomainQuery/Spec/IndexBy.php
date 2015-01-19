<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 19.1.2015
 * Time: 9:36
 */

namespace Elnino\DomainQuery\Spec;


use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\QueryBuilder;
use Elnino\DomainQuery\QueryBuilderModifierInterface;

class IndexBy implements QueryBuilderModifierInterface
{
    private $indexBy;

    /**
     * @param string $indexBy
     */
    public function __construct($indexBy)
    {
        $this->indexBy = $indexBy;
    }


    /**
     * Adds to QueryBuilder custom parameters, joins etc.
     *
     * @param QueryBuilder $qb
     * @return void
     */
    public function modifyQueryBuilder(QueryBuilder $qb)
    {
        /** @var From $from */
        $from = $qb->getDQLPart('from')[0];
        $qb->resetDQLPart('from');
        $qb->from($from->getFrom(), $from->getAlias(), $this->indexBy);
    }

}
