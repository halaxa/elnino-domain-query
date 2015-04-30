<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 30.4.2015
 * Time: 14:04
 */

namespace Elnino\DomainQuery\Spec;


use Elnino\DomainQuery\EntityClassProviderInterface;
use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\SpecInterface;

class EntityClass implements SpecInterface, EntityClassProviderInterface
{
    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var A specification
     */
    private $spec;

    /**
     * @param string $entityClass
     * @param $spec
     */
    function __construct($entityClass, $spec)
    {
        $this->entityClass = $entityClass;
        $this->spec = $spec;
    }

    /**
     * Provides entity class on which a spec implementing this iface is build
     *
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return SpecExpr|SpecInterface
     */
    public function expression($alias = null)
    {
        return $this->spec;
    }

}
