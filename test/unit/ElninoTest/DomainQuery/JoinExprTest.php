<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 7.2.14
 * Time: 10:44
 */

namespace ElninoTest\DomainQuery;


use Elnino\DomainQuery\JoinExpr;
use Doctrine\ORM\Query\Expr\Join;

class JoinExprTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParams()
    {
        $joinExpr = new JoinExpr('a', 'b', 'c', 'd', 'e', 'f');
        $expected = [
            'joinType' => 'a',
            'join' => 'b',
            'alias' => 'c',
            'conditionType' => 'd',
            'condition' => 'e',
            'indexBy' => 'f',
        ];
        $this->assertSame($expected, $joinExpr->getParams());
    }

    public function testIsLeft()
    {
        $join = new JoinExpr(Join::INNER_JOIN, 'entity.field', 'alias');
        $leftJoin = new JoinExpr(Join::LEFT_JOIN, 'entity.field', 'alias');

        $this->assertFalse($join->isLeft());
        $this->assertTrue($leftJoin->isLeft());
    }

    /**
     * @dataProvider staticFactoryTestData
     * @param $string
     * @param $expected
     */
    public function testStaticFactory($joinType, $string, $expected)
    {
        $joinExpr = JoinExpr::fromString($joinType, $string);
        $this->assertEquals($expected, $joinExpr);
    }

    /**
     * @return array
     */
    public function staticFactoryTestData()
    {
        return [
            [JoinExpr::INNER_JOIN, 'two',           new JoinExpr(JoinExpr::INNER_JOIN, 'two')],
            [JoinExpr::INNER_JOIN, 'two three',     new JoinExpr(JoinExpr::INNER_JOIN, 'two',     'three')],
            [JoinExpr::INNER_JOIN, 'one.two three', new JoinExpr(JoinExpr::INNER_JOIN, 'one.two', 'three')],
            [JoinExpr::INNER_JOIN, 'one.two',       new JoinExpr(JoinExpr::INNER_JOIN, 'one.two')],
            [JoinExpr::LEFT_JOIN,  'two',           new JoinExpr(JoinExpr::LEFT_JOIN, 'two')],
            [JoinExpr::LEFT_JOIN,  'two three',     new JoinExpr(JoinExpr::LEFT_JOIN, 'two',     'three')],
            [JoinExpr::LEFT_JOIN,  'one.two three', new JoinExpr(JoinExpr::LEFT_JOIN, 'one.two', 'three')],
            [JoinExpr::LEFT_JOIN,  'one.two',       new JoinExpr(JoinExpr::LEFT_JOIN, 'one.two')],
        ];
    }
}
