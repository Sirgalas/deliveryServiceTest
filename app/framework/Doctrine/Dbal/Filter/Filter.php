<?php

declare(strict_types=1);

namespace App\Framework\Doctrine\Dbal\Filter;

use App\Framework\Exception\LogicException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class Filter
{
    public const CACHE_FIELD = 'http_doctrine_dbal_filter_field';

    private readonly Connection $connection;
    private QueryBuilder $currentQueryBuilder;
    private readonly Query\Cache $cacher;

    public function __construct(Connection $connection, Query\Cache $cacher)
    {
        $this->connection = $connection;
        $this->currentQueryBuilder = $this->createQueryBuilder();
        $this->cacher = $cacher;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->connection);
    }

    public function apply(QueryBuilder $qb, Query\Configuration $configuration): QueryBuilder
    {
        return $this->setCurrentQueryBuilder($qb)
            ->select($configuration->filter)
            ->order($configuration->order)
            ->getCurrentQueryBuilder();
    }

    private function select(array $conditions): self
    {
        /**
         * @var string               $expr
         * @var array<string, array> $aliases
         */
        foreach ($conditions as $expr => $aliases) {
            foreach ($aliases as $alias => $values) {
                [$cacheKey,] = $this->cacher->generateCacheKeys(
                    self::CACHE_FIELD,
                    $this->currentQueryBuilder->getSQL(),
                    ['query' => "[{$expr}][{$alias}]"]
                );

                if (!($field = $this->cacher->get($cacheKey)) instanceof Query\Field) {
                    try {
                        $field = $this->createField($alias, $expr);
                    } catch (LogicException) {
                    }
                }

                if ($field instanceof Query\Field) {
                    $this->createQuery($field, $values, $cacheKey);
                }
            }
        }

        return $this;
    }

    private function order(array $conditions): self
    {
        /** @var string $alias */
        foreach ($conditions as $alias => $value) {
            $snakeCaseExprMethod = 'order_by';
            [$cacheKey,] = $this->cacher->generateCacheKeys(
                self::CACHE_FIELD,
                $this->currentQueryBuilder->getSQL(),
                ['query' => "[{$snakeCaseExprMethod}][{$alias}]"]
            );
            if (!($field = $this->cacher->get($cacheKey)) instanceof Query\Field) {
                try {
                    $field = $this->createField($alias, $snakeCaseExprMethod);
                } catch (LogicException) {
                }
            }

            if ($field instanceof Query\Field) {
                $this->createQuery($field, [$value], $cacheKey);
            }
        }

        return $this;
    }

    private function createQuery(Query\Field $field, array $values, string $cacheKey): void
    {
        (new Query\Builder($this->currentQueryBuilder))->andWhere($field->setValues($values));
        $this->cacher->set($cacheKey, $field);
    }

    private function createField(string $tableAliasAndColumnName, string $expr): Query\Field
    {
        foreach ($this->getTableAliases() as $alias) {
            if (0 === strncasecmp($tableAliasAndColumnName, $alias . '_', mb_strlen($alias . '_'))) {
                $columnName = mb_substr($tableAliasAndColumnName, mb_strlen($alias . '_'));

                return new Query\Field($expr, $alias, $columnName);
            }
        }

        throw new LogicException("{$tableAliasAndColumnName} not allowed");
    }

    /**
     * @return string[]
     */
    private function getTableAliases(): array
    {
        $tableAliases = [];

        $from = $this->currentQueryBuilder->getQueryPart('from');
        foreach ($from as $i => $item) {
            if (!\in_array($item['alias'], $tableAliases, true)) {
                $tableAliases[] = (string) $item['alias'];
            }
        }

        $joins = $this->currentQueryBuilder->getQueryPart('join');
        foreach ($joins as $i => $items) {
            foreach ($items as $item) {
                if (!\in_array($item['joinAlias'], $tableAliases, true)) {
                    $tableAliases[] = (string) $item['joinAlias'];
                }
            }
        }

        return $tableAliases;
    }

    private function getCurrentQueryBuilder(): QueryBuilder
    {
        return $this->currentQueryBuilder;
    }

    private function setCurrentQueryBuilder(QueryBuilder $qb): self
    {
        $this->currentQueryBuilder = $qb;

        return $this;
    }
}