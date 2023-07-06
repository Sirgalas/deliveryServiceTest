<?php

declare(strict_types=1);

namespace App\Doctrine\Dbal\Filter\Query;

use Webmozart\Assert\Assert;

class Field
{
    /**
     * Expression.
     */
    private readonly string $exprMethod;

    /**
     * Snake case DQL expression.
     */
    private readonly string $snakeCaseExprMethod;

    /**
     * Table alias in current instance DBAL\QueryBuilder.
     */
    private readonly string $tableAlias;

    /**
     * The column name. Optional. Defaults to the field name.
     */
    private readonly string $columnName;

    /**
     * Is LIKE operator?
     */
    private readonly bool $isLike;

    private array $values;

    public function __construct(string $snakeCaseExprMethod, string $tableAlias, string $columnName)
    {
        Assert::oneOf($snakeCaseExprMethod, Builder::SUPPORTED_EXPRESSIONS);
        $this->snakeCaseExprMethod = $snakeCaseExprMethod;
        $this->isLike = \in_array($snakeCaseExprMethod, ['like', 'not_like', 'ilike'], true);
        $exprMethod = lcfirst(str_replace('_', '', ucwords($snakeCaseExprMethod, '_')));
        Assert::methodExists(Builder::class, $exprMethod, "method \"{$exprMethod}\" not allowed");
        $this->exprMethod = $exprMethod;
        $this->tableAlias = $tableAlias;
        $this->columnName = $columnName;
    }

    public function getExprMethod(): string
    {
        return $this->exprMethod;
    }

    public function getSnakeCaseExprMethod(): string
    {
        return $this->snakeCaseExprMethod;
    }

    public function getTableAlias(): string
    {
        return $this->tableAlias;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }

    public function countValues(): int
    {
        return \count($this->values);
    }

    public function generateSQLParameter(string | int $i): string
    {
        return ":{$this->getTableAlias()}_{$this->getColumnName()}_$i";
    }

    public function generateParameter(string | int $i): string
    {
        return "{$this->getTableAlias()}_{$this->getColumnName()}_$i";
    }

    public function getPropertyPath(): string
    {
        return "{$this->getTableAlias()}.{$this->getColumnName()}";
    }

    public function isLike(): bool
    {
        return $this->isLike;
    }
}