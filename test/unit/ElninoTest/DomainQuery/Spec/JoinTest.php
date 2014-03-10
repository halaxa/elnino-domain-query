<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 26.2.14
 * Time: 15:28
 */

namespace ElninoTest\DomainQuery\Spec;

use Elnino\DomainQuery\SpecExpr;
use Elnino\DomainQuery\JoinExpr;
use Elnino\DomainQuery\Spec\Join;
use ElninoTest\SimpleMockTrait;

class JoinTest extends \PHPUnit_Framework_TestCase
{
    use SimpleMockTrait;

    private $exprMock;

    public function setUp()
    {
        $this->exprMock = $this->getMockSimply(SpecExpr::class, [
            'getExpression' => (new \Doctrine\ORM\Query\Expr())->eq('a', 'b'),
            'getBinds' => [],
            'getJoins' => []
        ],[
            $this->any(),
            $this->any(),
            $this->any(),
        ]);
    }

    public function testJoinCreatesAliasIfNoAliasInJoinExpr()
    {
        $join = new Join(
            new JoinExpr(JoinExpr::INNER_JOIN, 'test.text'),
            $this->exprMock
        );
        $this->assertSame("text_0", $join->expression()->getJoins()[0]->getAlias());
    }

    public function testExpressionWorksIfAliasInJoinExprAndParameterIsExpr()
    {
        $join = new Join(
            new JoinExpr(JoinExpr::INNER_JOIN, 'test.text', 'alias'),
            $this->exprMock
        );
        $this->assertInstanceOf(SpecExpr::class, $join->expression(null));
    }

    public function testJoinNeedsNoSecondParam()
    {
        $join = new Join('field');
        $this->assertInstanceOf(SpecExpr::class, $join->expression(null));
    }
}
