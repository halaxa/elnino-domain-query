<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 19.2.14
 * Time: 14:13
 */

namespace Elnino\DomainQuery\Spec;


use Elnino\DomainQuery\SpecInterface;
use Elnino\DomainQuery\SpecExpr;

/**
 * Decorates ExpressionSpecificationInterface with additional params.
 *
 * @package Commons\Persistence\Spec
 */
class Params implements SpecInterface
{
    /** @var  array */
    private $params;

    /**
     * @param array $params  Format: ['a.field' => 'value']
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Expression describing the specification.
     *
     * @param string $alias
     * @return \Elnino\DomainQuery\SpecExpr
     */
    public function expression($alias = null)
    {
        $e = new \Doctrine\ORM\Query\Expr();
        $i = 0;
        $exprs = $binds = [];

        foreach ($this->params as $key => $value) {
            list($alias, $field) = strpos($key, '.') ? explode('.', $key) : [$alias, $key];
            $param = ":{$alias}_{$field}_" . $i++;

            $binds[$param] = $value;
            if (is_array($value)) {
                $op = 'in';
            } elseif (is_string($value) && strpos($value, '%') !== false) {
                $op = 'like';
            } elseif ($value === null) {
                $op = 'isNull';
            } else {
                $op = 'eq';
            }
            $exprs[] = $e->{$op}("$alias.$field", $param);
        }

        return new SpecExpr(
            call_user_func_array([$e, 'andX'], $exprs),
            $binds
        );
    }
}
