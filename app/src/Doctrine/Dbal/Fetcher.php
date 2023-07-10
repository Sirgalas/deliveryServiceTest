<?php

declare(strict_types=1);

namespace App\Doctrine\Dbal;

use App\Doctrine\Dbal\Filter\Filter;
use App\Doctrine\Dbal\Filter\Query\Cache;
use App\Exception\TableNotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Webmozart\Assert\Assert;

class Fetcher
{
    protected Connection $connection;
    //private readonly Filter $filter;
    private readonly PaginatorInterface $paginator;

    public function __construct(
        Connection $connection,
        PaginatorInterface $paginator,
        //Cache $cacher,
    ) {
        $this->connection = $connection;
       // $this->filter = new Filter($connection, $cacher);
        $this->paginator = $paginator;
    }

    final public function executeQuery(QueryBuilder $queryBuilder): Result
    {
        return $queryBuilder->executeQuery();
    }

    final public function getQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    final public function selectExistsQuery(QueryBuilder $queryBuilder): bool
    {
        return (bool) $this->getQueryBuilder()
            ->select('EXISTS(' . SQLPreparer::prepare($this->connection, $queryBuilder) . ')')
            ->executeQuery()
            ->fetchOne();
    }

    /*final public function getFilter(): Filter
    {
        return $this->filter;
    }*/

    final public function isExistTable(string $tableName): bool
    {
        $dbalResult = $this->executeQuery($this->getQueryBuilder()->select("to_regclass('public.{$tableName}')"));

        return \is_string($dbalResult->fetchOne());
    }

    final public function isExistColumn(string $tableName, string $column): bool
    {
        if ($this->isExistTable($tableName)) {
            return \in_array($column, $this->getColumnsNamesByTable($tableName), true);
        }

        throw new TableNotFoundException("Table {$tableName} not found");
    }

    final public function isExistColumns(string $tableName, array $columns): bool
    {
        if ($this->isExistTable($tableName)) {
            return [] === array_diff($columns, $this->getColumnsNamesByTable($tableName));
        }

        throw new TableNotFoundException("Table {$tableName} not found");
    }

    final public function getColumnsNamesByTable(string $tableName): array
    {
        return array_keys($this->connection->createSchemaManager()->listTableColumns($tableName));
    }

    final public function find(int | string $id, string $tableName, string $columnName = 'id'): bool
    {
        $qb = $this->getQueryBuilder()
            ->select(["COUNT({$columnName})"])
            ->from($tableName)
            ->where("{$columnName} = :value")
            ->setParameter('value', $id)
        ;

        return $this->executeQuery($qb)->fetchOne() > 0;
    }

    final public function findByUniqueColumn(string $tableName, string $columnName, mixed $value): bool
    {
        $qb = $this->getQueryBuilder()
            ->select(['COUNT(id)'])
            ->from($tableName, 't')
            ->where("t.$columnName = :value")
            ->setParameter('value', $value)
        ;

        return 1 === $this->executeQuery($qb)->fetchOne();
    }

    final public function existByCriteria(string $tableName, array $criteria): bool
    {
        $qb = $this->getQueryBuilder();
        $expr = $qb->expr();
        $qb->select(['COUNT(id)'])
            ->from($tableName, 'tableName')
        ;

        foreach ($criteria as $columnName => $value) {
            $qb
                ->andWhere($expr->eq("tableName.{$columnName}", ":{$columnName}"))
                ->setParameter($columnName, $value);
        }

        return 0 !== $this->executeQuery($qb)->fetchOne();
    }

    public function findByIds(array $ids, string $tableName): array
    {
        Assert::isNonEmptyList($ids);
        $id = $ids[0];
        $expr = $this->getQueryBuilder()->expr();
        $qb = $this->getQueryBuilder()
            ->select(['id'])
            ->from($tableName)
            ->where($expr->in('id', ':ids'))
            ->setParameter(
                'ids',
                $ids,
                \is_string($id)
                    ? Connection::PARAM_STR_ARRAY
                    : Connection::PARAM_INT_ARRAY
            )
        ;

        return $this->executeQuery($qb)->fetchFirstColumn();
    }

    final public function paginate(mixed $target, int $page, int $limit, array $options = []): PaginationInterface
    {
        return $this->paginator->paginate($target, $page, $limit, $options);
    }

    final public function getPreparedQueryVarsArray(array $arr): string
    {
        return implode(',', array_map(fn ($x) => "'{$x}'", $arr));
    }
}