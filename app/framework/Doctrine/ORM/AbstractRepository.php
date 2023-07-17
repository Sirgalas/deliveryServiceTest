<?php

declare(strict_types=1);

namespace App\Framework\Doctrine\ORM;

use App\Framework\Security\Framework\Exceptions\EntityNotFoundException;
use App\Framework\Security\Framework\Exceptions\NotExistClassException;
use App\Framework\Security\Framework\Exceptions\UnexpectedClassException;
use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Configuration\Connection\ConnectionLoader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

class AbstractRepository  implements ConnectionLoader
{
    protected EntityManagerInterface $em;
    protected ObjectRepository $repository;
    /** @psalm-var class-string */
    private readonly string $targetClass;

    /**
     * @psalm-param class-string<T> $targetClass
     */
    public function __construct(EntityManagerInterface $em, string $targetClass)
    {
        if (false === class_exists($targetClass)) {
            throw new NotExistClassException($targetClass);
        }

        $this->em = $em;
        $this->targetClass = $targetClass;

        $this->repository = $em->getRepository($targetClass);
    }

    /**
     * @psalm-param T
     */
    final public function add(object $object): void
    {
        if (!$object instanceof $this->targetClass) {
            throw new UnexpectedClassException($this->targetClass, $object::class);
        }
        $this->em->persist($object);
    }

    /**
     * @psalm-return T
     *
     * @throws EntityNotFoundException
     */
    public function get(mixed $id)
    {
        /** @var T $object */
        $object = $this->repository->find($id);
        if ($object instanceof $this->targetClass) {
            $this->em->initializeObject((object) $object);

            return $object;
        }

        throw new EntityNotFoundException("{$this->targetClass} by id: {$id} - not found");
    }

    /**
     * @psalm-return T|null
     */
    final public function find(mixed $id)
    {
        /** @var T|null $object */
        $object = $this->repository->find($id);
        if ($object instanceof $this->targetClass) {
            return $object;
        }

        return null;
    }

    /**
     * @psalm-param class-string<T>
     *
     * @psalm-return T
     */
    final public function findByCompositeKey(string $className, array $keys): mixed
    {
        if (false === class_exists($className)) {
            throw new NotExistClassException($className);
        }

        /** @var T|null $object */
        $object = $this->em->find($className, $keys);
        if ($object instanceof $this->targetClass) {
            return $object;
        }

        return null;
    }

    /**
     * @psalm-param class-string<T>
     *
     * @psalm-return T
     */
    final public function getByCompositeKey(string $className, array $keys): mixed
    {
        if (false === class_exists($className)) {
            throw new NotExistClassException($className);
        }

        /** @var T|null $object */
        $object = $this->em->find($className, $keys);
        if ($object instanceof $this->targetClass) {
            return $object;
        }

        $jsonKeys = json_encode($keys, \JSON_THROW_ON_ERROR);

        throw new EntityNotFoundException("{$this->targetClass} by composite keys: {$jsonKeys} - not found");
    }

    /**
     * @psalm-param T
     */
    final public function remove(object $object): void
    {
        if (!$object instanceof $this->targetClass) {
            throw new UnexpectedClassException($this->targetClass, $object::class);
        }

        $this->em->remove($object);
    }

    /**
     * @psalm-param T
     */
    final public function refresh(object $object): void
    {
        $this->em->refresh($object);
    }

    /**
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArgumentTypeCoercion
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     *
     * @psalm-return T[]
     */
    final public function findBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        // phpcs:disable
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
        // phpcs:enable
    }

    final public function removeBy(array $criteria): void
    {
        /** @var object $object */
        foreach ($this->findBy($criteria) as $object) {
            $this->remove($object);
        }
    }

    /**
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArgumentTypeCoercion
     *
     * @psalm-return T|null
     */
    final public function findOneBy(array $criteria)
    {
        /** @var T|null $object */
        $object = $this->repository->findOneBy($criteria);

        return ($object instanceof $this->targetClass) ? $object : null;
    }

    /**
     * @psalm-suppress MixedAssignment
     *
     * @psalm-return T[]
     */
    final public function findByIds(array $ids): array
    {
        if ([] === $ids) {
            return [];
        }

        $expr = $this->em->getExpressionBuilder();
        $qb = $this->em->createQueryBuilder();
        $qb->select('t1')
            ->from($this->targetClass, 't1')
            ->where($expr->in('t1.id', $ids))
        ;

        /** @var T[]|null $result */
        $result = $qb->getQuery()->getResult();

        return \is_array($result) ? $result : [];
    }

    final public function createQueryBuilder(): QueryBuilder
    {
        return $this->em->createQueryBuilder();
    }

    final public function expr(): Expr
    {
        return $this->em->getExpressionBuilder();
    }

    final public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    final public function getConnection(?string $name = null): Connection
    {
        return $this->em->getConnection();
    }
}