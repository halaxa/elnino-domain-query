<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 12.2.14
 * Time: 15:46
 */

namespace Elnino\DomainQuery;


interface ResultModifierInterface
{
    /**
     * Modifies final result set
     *
     * @param array $result
     * @return void
     */
    public function modifyResult(array & $result);
}
