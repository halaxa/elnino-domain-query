<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 12.2.14
 * Time: 14:43
 */

namespace ElninoTest\DomainQuery\Spec;

use Elnino\DomainQuery\Spec\AbstractOperator;
use Elnino\DomainQuery\JoinExpr;
use Elnino\DomainQuery\Spec\AndX;
use Elnino\DomainQuery\SpecExpr;
use ElninoTest\DomainQuery\Entity\DoneTodoSpec;
use ElninoTest\DomainQuery\Entity\RichUnblockedSpec;
use Doctrine\ORM\Query\Expr;
use ElninoTest\SimpleMockTrait;

class AbstractOperatorTest extends \PHPUnit_Framework_TestCase
{
    use SimpleMockTrait;

    public function setUp()
    {
        $this->assertTrue(
            in_array(AbstractOperator::class, class_parents(AndX::class)),
            "AndX must extend AbstractOperator"
        );
    }

    public function testConstructorThrowsOnInvalidArgumentTypes()
    {
        $this->setExpectedException('InvalidArgumentException');
        new AndX($this->getMockSimply(SpecExpr::class), 'wrong');
    }

    public function testConstructorAcceptsTypes()
    {
        new AndX(
            (new RichUnblockedSpec())->expression(),
            (new DoneTodoSpec())->expression()
        );
        new AndX(
            new RichUnblockedSpec(),
            new DoneTodoSpec()
        );
    }

    public function testExpressionAggregatesJoins()
    {
        $joins1 = [new JoinExpr(JoinExpr::LEFT_JOIN,  'a.b', 'c')];
        $joins2 = [new JoinExpr(JoinExpr::INNER_JOIN, 'd.e', 'f')];
        $binds1 = [':one' => 'two', ':three' => 'four'];
        $binds2 = [':five' => 'six'];

        $and = new AndX(
            $this->getMockSimply(SpecExpr::class,[
                'getJoins' => $joins1,
                'getBinds' => $binds1
            ]),
            $this->getMockSimply(SpecExpr::class,[
                'getJoins' => $joins2,
                'getBinds' => $binds2
            ])
        );
        $this->assertSame(array_merge($joins1, $joins2), $and->expression()->getJoins());
        $this->assertSame(array_merge($binds1, $binds2), $and->expression()->getBinds());
    }
}
