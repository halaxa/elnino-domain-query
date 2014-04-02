<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 27.1.14
 * Time: 8:28
 */

namespace Elnino\DomainQuery;


use Elnino\DomainQuery\Spec\Join;
use Doctrine\ORM\EntityManager;
use Elnino\DomainQuery\SpecExpr as Expression;
use Elnino\DomainQuery\SpecInterface as Specification;

/**
 * Default implementation of SpecificationRepositoryInterface
 */
class DefaultSpecificationRepository implements SpecificationRepositoryInterface
{
    /** @var  string */
    private $entityClass;

    /** @var  EntityManager */
    private $em;

    /**
     * @param string        $entityClass
     * @param EntityManager $em
     */
    public function __construct($entityClass, EntityManager $em)
    {
        $this->entityClass = $entityClass;
        $this->em = $em;
    }

    /**
     * Returns entities from persistence layer matching given specification.
     *
     * @param string $select
     * @param mixed  $spec One or more specifications
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws InvalidStateException
     */
    public function match($select = null, $spec = null/*, ... */)
    {
        $args = func_get_args();
        $qb = $this->em->createQueryBuilder();

        if (is_string($select)) {
            $mainAlias = explode('.', trim(explode(',', $select)[0]))[0];
            $specs = array_slice($args, 1);
        } else {
            $mainAlias = strtolower(array_reverse(explode('\\', $this->entityClass))[0]);
            $select = $mainAlias;
            $specs = $args;
        }

        $qb->select($select)
           ->from($this->entityClass, $mainAlias);

        /** @var QueryModifierInterface[] $queryModifiers */
        $queryModifiers  = [];
        /** @var ResultModifierInterface[] $resultModifiers */
        $resultModifiers = [];
        /** @var ResultFetcherInterface $resultFetcher */
        $resultFetcher = null;

        foreach ($specs as $spec) {
            $validClass = false;
            $expr = $spec;

            if ($expr instanceof SpecInterface) {
                $expr = self::getExprFromSpec($expr, $mainAlias);
                $validClass = true;
            }

            if ($expr instanceof SpecExpr) {
                foreach ($expr->getJoins() as $join) {
                    $joinFunc = $join->isLeft() ? 'leftJoin' : 'join';
                    call_user_func_array([$qb, $joinFunc], array_slice($join->getParams(), 1));
                }
                foreach ($expr->getBinds() as $param => $value) {
                    $qb->setParameter($param, $value);
                }
                $where = $expr->getExpression();
                if ($where) {
                    $qb->andWhere($where);
                }
            }

            if ($spec instanceof QueryBuilderModifierInterface) {
                $spec->modifyQueryBuilder($qb);
                $validClass = true;
            }

            if ($spec instanceof QueryModifierInterface) {
                $queryModifiers[] = $spec;
                $validClass = true;
            }

            if ($spec instanceof ResultModifierInterface) {
                $resultModifiers[] = $spec;
                $validClass = true;
            }

            if ($spec instanceof ResultFetcherInterface) {
                $resultFetcher = $spec;
                $validClass = true;
            }

            if ( ! $validClass) {
                throw new \InvalidArgumentException(sprintf(
                    'Given specification must be one of %s, %s, %s, %s, %s or %s. Given %s',
                    Specification::class,
                    Expression::class,
                    QueryBuilderModifierInterface::class,
                    QueryModifierInterface::class,
                    ResultModifierInterface::class,
                    ResultFetcherInterface::class,
                    is_object($spec) ? get_class($spec) : gettype($spec)
                ));
            }
        }

        $query = $qb->getQuery();

        foreach ($queryModifiers as $queryModifier) {
            $queryModifier->modifyQuery($query);
        }

        if ($resultFetcher) {
            $result = $resultFetcher->fetchResult($query);
        } else {
            $result = $query->getResult();
        }

        Join::_queryFinished();

        foreach ($resultModifiers as $resultModifier) {
            $resultModifier->modifyResult($result);
        }

        return $result;
    }

    /**
     * @param Specification $expr
     * @param string        $alias
     * @return SpecExpr
     * @throws InvalidStateException
     */
    static public function getExprFromSpec($expr, $alias)
    {
        while ($expr instanceof Specification) {
            $oldExpr = $expr;
            $expr = $expr->expression($alias);
            if ($oldExpr === $expr) {
                throw new InvalidStateException(sprintf(
                    'Infinite recursion detected. An instance of %s returns itself via expression().',
                    get_class($expr)
                ));
            }
        }

        return $expr;
    }

    /**
     * Returns one entity by id or null when not found in persistence layer.
     *
     * @param mixed $id
     * @return null|object
     */
    public function find($id)
    {
        return $this->em->find($this->entityClass, $id);
    }

    /**
     * Tells the ObjectManager to make an instance managed and persistent.
     *
     * The object will be entered into the database as a result of the flush operation.
     *
     * NOTE: The persist operation always considers objects that are not yet known to
     * this ObjectManager as NEW. Do not pass detached objects to the persist operation.
     *
     * @param object $object The instance to make managed and persistent.
     *
     * @return void
     */
    public function persist($object)
    {
        $this->em->persist($object);
    }

    /**
     * Removes an object instance.
     *
     * A removed object will be removed from the database as a result of the flush operation.
     *
     * @param object $object The object instance to remove.
     *
     * @return void
     */
    public function remove($object)
    {
        $this->em->remove($object);
    }

    /**
     * Merges the state of a detached object into the persistence context
     * of this ObjectManager and returns the managed copy of the object.
     * The object passed to merge will not become associated/managed with this ObjectManager.
     *
     * @param object $object
     *
     * @return object
     */
    public function merge($object)
    {
        return $this->em->merge($object);
    }

    /**
     * Clears the ObjectManager. All objects that are currently managed
     * by this ObjectManager become detached.
     *
     * @param string|null $objectName if given, only objects of this type will get detached.
     *
     * @return void
     */
    public function clear($objectName = null)
    {
        $this->em->clear($objectName);
    }

    /**
     * Detaches an object from the ObjectManager, causing a managed object to
     * become detached. Unflushed changes made to the object if any
     * (including removal of the object), will not be synchronized to the database.
     * Objects which previously referenced the detached object will continue to
     * reference it.
     *
     * @param object $object The object to detach.
     *
     * @return void
     */
    public function detach($object)
    {
        $this->em->detach($object);
    }

    /**
     * Refreshes the persistent state of an object from the database,
     * overriding any local changes that have not yet been persisted.
     *
     * @param object $object The object to refresh.
     *
     * @return void
     */
    public function refresh($object)
    {
        $this->em->refresh($object);
    }

    /**
     * Flushes all changes to objects that have been queued up to now to the database.
     * This effectively synchronizes the in-memory state of managed objects with the
     * database.
     *
     * @return void
     */
    public function flush()
    {
        $this->em->flush();
    }

    /**
     * Checks if the object is part of the current UnitOfWork and therefore managed.
     *
     * @param object $object
     *
     * @return bool
     */
    public function contains($object)
    {
        return $this->em->contains($object);
    }


}
