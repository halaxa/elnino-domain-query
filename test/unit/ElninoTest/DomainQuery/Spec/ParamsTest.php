<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 19.2.14
 * Time: 15:19
 */

namespace ElninoTest\DomainQuery\Spec;

use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\Spec\Params;
use ElninoTest\SimpleMockTrait;

class ParamsTest extends \PHPUnit_Framework_TestCase
{
    use SimpleMockTrait;

    public function testGeneratesParams()
    {
        $values = [
            'a.field' => 'one',
            'b.field' => 'two'
        ];

        $e = new \Doctrine\ORM\Query\Expr();
        $expected = new SpecExpr(
            $e->andX(
                $e->eq('a.field', ':a_field_0'),
                $e->eq('b.field', ':b_field_1')
            ),
            [
                ':a_field_0' => 'one',
                ':b_field_1' => 'two',
            ]
        );

        $byParams = new Params($values);
        $this->assertEquals($expected, $byParams->expression());

    }

    public function testUsesGivenAliasWhenNonePresentInParams()
    {
        $values = [
            'field' => 'one',
            'b.field' => 'two'
        ];

        $e = new \Doctrine\ORM\Query\Expr();
        $expected = new SpecExpr(
            $e->andX(
                $e->eq('test.field', ':test_field_0'),
                $e->eq('b.field', ':b_field_1')
            ),
            [
                ':test_field_0' => 'one',
                ':b_field_1' => 'two',
            ]
        );

        $byParams = new Params($values);
        $this->assertEquals($expected, $byParams->expression('test'));
    }
}
