<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 9.4.2014
 * Time: 8:19
 */

namespace Elnino\DomainQuery;


interface EntityClassProviderInterface
{
    /**
     * Provides entity class on which a spec implementing this iface is build
     *
     * @return string
     */
    public function getEntityClass();
}
