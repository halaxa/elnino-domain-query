<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 29.1.14
 * Time: 9:02
 */

namespace ElninoTest\DomainQuery;

use Elnino\DomainQuery\DefaultSpecificationRepository;
use Elnino\DomainQuery\InvalidStateException;
use Elnino\DomainQuery\JoinExpr;
use Elnino\DomainQuery\QueryBuilderModifierInterface;
use Elnino\DomainQuery\QueryModifierInterface;
use Elnino\DomainQuery\ResultFetcherInterface;
use Elnino\DomainQuery\ResultModifierInterface;
use Elnino\DomainQuery\Spec\Params;
use Elnino\DomainQuery\Spec\LeftJoin;
use Elnino\DomainQuery\Spec\AndX;
use Elnino\DomainQuery\Spec\Join;
use Elnino\DomainQuery\Spec\NotX;
use Elnino\DomainQuery\Spec\OrX;
use Elnino\MockingTrait;
use ElninoTest\DomainQuery\Entity\ArrayResultFetcher;
use ElninoTest\DomainQuery\Entity\DoneTodoSpec;
use ElninoTest\DomainQuery\Entity\MasterUnblockedSpec;
use ElninoTest\DomainQuery\Entity\Person;
use ElninoTest\DomainQuery\Entity\RecursiveSpec;
use ElninoTest\DomainQuery\Entity\RichSpec;
use ElninoTest\DomainQuery\Entity\RichUnblockedSpec;
use ElninoTest\DomainQuery\Entity\RichUnblockedWithPendingTodos;
use ElninoTest\DomainQuery\Entity\RichUnblockedWithPendingTodosDefaultAliases;
use ElninoTest\DomainQuery\Entity\SelectProviderSpec;
use ElninoTest\DomainQuery\Entity\Todo;
use ElninoTest\DomainQuery\Entity\UnblockedSpec;
use ElninoTest\DomainQuery\Entity\WellRatedSpec;
use ElninoTest\DomainQuery\Entity\WrappedUnblockedSpec;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use ElninoTest\FunctionalTrait;

class DefaultSpecificationRepositoryTest extends \PHPUnit_Extensions_Database_TestCase
{
    use MockingTrait;
    use FunctionalTrait;

    /** @var  DefaultSpecificationRepository */
    private $repo;

    /** @var  Query\Expr */
    private $e;

    /** @var  DebugStack */
    private $logger;

    public function getConnection()
    {
        return new \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($this->getPdo());
    }

    public function getDataset()
    {
        return new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . '/Entity/testData.yml');
    }

    public function setUp()
    {
        parent::setUp();
        $this->repo = new DefaultSpecificationRepository(
            $this->getEm(),
            Entity\Person::class
        );
        $this->e = new Query\Expr();
        $this->logger = new DebugStack();
        $this->getEm()->getConnection()->getConfiguration()->setSQLLogger($this->logger);
    }

    /**
     * @TODO move to unit tests
     */
    public function testEntityManagerMethodsAreProxied()
    {
        $em = $this->getMockSimply(EntityManager::class, [
            'find' => true,
            'persist' => true,
            'remove' => true,
            'merge' => true,
            'clear' => true,
            'detach' => true,
            'refresh' => true,
            'flush' => true,
            'contains' => true,
            'transactional' => true
        ]);

        $repo = new DefaultSpecificationRepository($em, Person::class);

        $repo->find(1);
        $repo->persist(new \stdClass());
        $repo->remove(new \stdClass());
        $repo->merge(new \stdClass());
        $repo->clear();
        $repo->detach(new \stdClass());
        $repo->refresh(new \stdClass());
        $repo->flush();
        $repo->contains(new \stdClass());
        $repo->transactional(function(){});
    }

    /**
     * @TODO move to unit tests
     */
    public function testMatchThrowsOnInvalidSpecificationClass()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->repo->match(new \stdClass());
    }

    /**
     * @TODO move to unit tests
     */
    public function testMatchCallsQueryBuilderModifier()
    {
        $mock = $this->getMockSimply(QueryBuilderModifierInterface::class, [
            'modifyQueryBuilder' => null
        ]);
        $this->repo->match($mock);
    }

    /**
     * @TODO move to unit tests
     */
    public function testMatchCallsQueryModifier()
    {
        $mock = $this->getMockSimply(QueryModifierInterface::class, [
            'modifyQuery' => null
        ]);
        $this->repo->match($mock);
    }

    /**
     * @TODO move to unit tests
     */
    public function testMatchCallsResultModifier()
    {
        $mock = $this->getMockSimply(ResultModifierInterface::class, [
            'modifyResult' => null
        ]);
        $this->repo->match($mock);
    }

    /**
     * @TODO move to unit tests
     */
    public function testMatchCallsResultFetcher()
    {
        $mock = $this->getMockSimply(ResultFetcherInterface::class, [
            'fetchResult' => []
        ]);
        $this->repo->match($mock);
    }

    public function testEmptyMatchReturnsAllEntities()
    {
        $this->assertCount(3, $this->repo->match());
    }

    public function testMatchWithAliasOnlyReturnsAllEntities()
    {
        $this->assertCount(3, $this->repo->match('dummy'));
    }


    public  function testMatchWithAliasFields()
    {
        $this->assertCount(3, $this->repo->match('dummy.id, dummy.salary'));
    }

    public  function testMatchWithAliasFieldsAndFether()
    {
        $exp = [
            ['id' => 1, 'salary' => 20000],
            ['id' => 2, 'salary' => 25000],
            ['id' => 3, 'salary' => 30000],
        ];

        $res = $this->repo->match('dummy.id, dummy.salary', new ArrayResultFetcher());

        $this->assertEquals($exp, $res);
    }

    public function testMatchWithMoreThanOneAliasesInSelectWithoutJoinFails()
    {
        $this->setExpectedException(Query\QueryException::class);
        $this->repo->match('a,b');
    }

    public function testSimpleSpecWithoutAlias()
    {
        $this->assertCount(2, $this->repo->match(new RichUnblockedSpec()));
    }

    public function testSimpleSpecWithAlias()
    {
        $this->assertCount(2, $this->repo->match('dummy', new RichUnblockedSpec()));
    }

    public function testCompositeSpec()
    {
        $this->assertCount(1, $this->repo->match(new RichUnblockedWithPendingTodos()));
    }

    public function testSpecWithDefaultAliases()
    {
        $this->assertCount(1, $this->repo->match(new RichUnblockedWithPendingTodosDefaultAliases()));
    }

    public function testDirectInputOperatorWithoutAliases()
    {
        $result = $this->repo->match(
            new AndX(
                new RichSpec(),
                new UnblockedSpec()
            )
        );
    }

    public function testDirectInputJoinWithoutAliases()
    {
        $result = $this->repo->match(
            new Join(
                new JoinExpr(JoinExpr::INNER_JOIN, "todos"),
                new DoneTodoSpec()
            )
        );
    }

    public function testDirectInputJoinWithoutAliasesAndOperator()
    {
        $result = $this->repo->match(
            new Join(
                new JoinExpr(JoinExpr::INNER_JOIN, "todos"),
                new NotX(new DoneTodoSpec())
            )
        );
    }

    public function testMatchSupportsRecursiveSpecs()
    {
        $result = $this->repo->match(new WrappedUnblockedSpec());
        $this->assertCount(2, $result);
    }

    public function testMatchDetectsRecursionWhenSpecReturnsItself()
    {
        $this->setExpectedException(InvalidStateException::class);
        $this->repo->match(new RecursiveSpec());
    }

    public function testOperatorSupportsRecursiveSpecs()
    {
        $result = $this->repo->match(new NotX(new WrappedUnblockedSpec));
        $this->assertCount(1, $result);
    }

    public function testOperatorDetectsRecursionWhenSpecReturnsItself()
    {
        $this->setExpectedException(InvalidStateException::class);
        $this->repo->match(new NotX(new RecursiveSpec));
    }

    public function testJoinSupportsRecursiveSpec()
    {
        $repo = new DefaultSpecificationRepository($this->getEm(), Todo::class);
        $result = $repo->match(new Join('person', new WrappedUnblockedSpec));
        $this->assertCount(3, $result);
    }

    public function testJoinDetectsRecursionWhenSpecReturnsItself()
    {
        $this->setExpectedException(InvalidStateException::class);
        $this->repo->match(new Join('test', new RecursiveSpec));
    }

    public function testAutoAliasesInTwoJoins()
    {
        /** @var Person[] $result */
        $result = $this->repo->match(
            new UnblockedSpec(),
            new Join(
                new JoinExpr(JoinExpr::INNER_JOIN, 'todos', 'todo'),
                new Join(
                    new JoinExpr(JoinExpr::INNER_JOIN, 'ratings'),
                    new WellRatedSpec()
                )
            )
        );
        $this->assertCount(1, $result);
        $this->assertSame(2, $result[0]->getId());
    }

    public function testAutoGeneratedAlias()
    {
        /** @var Person[] $result */
        $result = $this->repo->match(
            new UnblockedSpec(),
            new Join(
                new JoinExpr(JoinExpr::INNER_JOIN, 'todos'),
                new Join(
                    new JoinExpr(JoinExpr::INNER_JOIN, 'ratings'),
                    new WellRatedSpec()
                )
            )
        );
        $this->assertCount(1, $result);
        $this->assertSame(2, $result[0]->getId());
    }

    public function testShortJoinNotation()
    {
        /** @var Person[] $result */
        $result = $this->repo->match('person',
            new UnblockedSpec(),
            new Join('person.todos todo',
                new Join('todo.ratings rating',
                    new WellRatedSpec()
                )
            )
        );
        $this->assertCount(1, $result);
        $this->assertSame(2, $result[0]->getId());
    }

    public function testShortJoinNotationWithFetchJoin()
    {
        $this->assertCount(0, $this->logger->queries);
        /** @var Person[] $result */
        $result = $this->repo->match('person, todo, rating',
            new UnblockedSpec(),
            new Join('person.todos todo',
                new Join('todo.ratings rating',
                    new WellRatedSpec()
                )
            )
        );
        $this->assertCount(1, $result);
        $this->assertSame(2, $result[0]->getId());

        $this->assertCount(1, $this->logger->queries);
        iterator_to_array($result[0]->getTodos());
        $this->assertCount(1, $this->logger->queries);
    }

    public function testShortJoinNotationWithoutSelect()
    {
        /** @var Person[] $result */
        $result = $this->repo->match(
            new UnblockedSpec(),
            new Join('todos',
                new Join('ratings',
                    new WellRatedSpec()
                )
            )
        );

        $this->assertCount(1, $result);
        $this->assertSame(2, $result[0]->getId());
    }

    public function testShortJoinNotationWithSelect()
    {
        /** @var Person[] $result */
        $result = $this->repo->match('person',
            new UnblockedSpec(),
            new Join('todos',
                new Join('ratings',
                    new WellRatedSpec()
                )
            )
        );
        $this->assertCount(1, $result);
        $this->assertSame(2, $result[0]->getId());
    }

    public function testConditionOnTwoJoins()
    {
        /** @var Person[] $result */
        $result = $this->repo->match(
            new UnblockedSpec(),
            new OrX(
                new Join('todos',
                    new LeftJoin('ratings',
                        new WellRatedSpec()
                    )
                ),
                new LeftJoin('ratings',
                    new WellRatedSpec()
                )
            )
        );

        $this->assertCount(2, $result);
        $this->assertSame(1, $result[0]->getId());
        $this->assertSame(2, $result[1]->getId());
    }

    public function testMatchRecognizesQueryModifierInterface()
    {
        // make sure it returns 2 results without LIMIT
        $preResult = $this->repo->match(new UnblockedSpec());
        $this->assertCount(2, $preResult);

        // make sure it returns 1 result asked by QueryModifierInterface
        $result = $this->repo->match(new MasterUnblockedSpec());
        $this->assertCount(1, $result);
    }

    public function testSelectProviderForcesFetchJoin()
    {
        $this->assertCount(0, $this->logger->queries);
        /** @var Person[] $result */
        $result = $this->repo->match(new SelectProviderSpec());

        $this->assertCount(1, $result);
        $this->assertSame(2, $result[0]->getId());

        $this->assertCount(1, $this->logger->queries);
        iterator_to_array($result[0]->getTodos());
        $this->assertCount(1, $this->logger->queries);
    }

    public function testByParamsIntegration()
    {
        $result = $this->repo->match(new Params([
            'blocked' => 0,
            'salary'  => 25000
        ]));
        $this->assertCount(1, $result);
    }

    public function testByParamsIntegrationWithAliases()
    {
        $result = $this->repo->match('p', new Params([
            'p.blocked' => 0,
            'p.salary'  => 25000
        ]));
        $this->assertCount(1, $result);
    }

    public function testJoinNeedsNoSpec()
    {
        $result = $this->repo->match(new Join('todos'));
        $this->assertCount(3, $result);
    }

    public function testEntityClassIsOptional()
    {
        $repo = new DefaultSpecificationRepository($this->getEm());
    }

    public function testClassTakenFromSpecTakesPrecedence()
    {
        $repo = new DefaultSpecificationRepository($this->getEm(), Todo::class);
        $spec = new MasterUnblockedSpec;

        $this->assertSame(Person::class, $spec->getEntityClass());

        $result = $repo->match($spec);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Person::class, $result[0]);
    }

    public function testThrowsIfNoEntityClassAvailable()
    {
        $repo = new DefaultSpecificationRepository($this->getEm());

        $this->setExpectedException(InvalidStateException::class, 'No master entity class available.');
        $repo->match(new UnblockedSpec);
    }

    public function testWithoutEntityClassWithProvider()
    {
        $repo = new DefaultSpecificationRepository($this->getEm());
        $spec = new MasterUnblockedSpec;

        $this->assertSame(Person::class, $spec->getEntityClass());

        $result = $repo->match($spec);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Person::class, $result[0]);
    }

    public function testCallsDqlLoggerIfPresent()
    {
        $loggerMock = function ($dql) {
            $this->assertSame(
                'SELECT person FROM ElninoTest\DomainQuery\Entity\Person person WHERE person.blocked = :blocked',
                $dql
            );
        };

        $repo = new DefaultSpecificationRepository($this->getEm());
        $spec = new MasterUnblockedSpec;

        $repo->setDqlLogger($loggerMock);
        $result = $repo->match($spec);
    }
}
