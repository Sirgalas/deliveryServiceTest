<?php

declare(strict_types=1);

namespace App\Framework\Doctrine\Dbal\Filter\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Webmozart\Assert\Assert;

class Builder
{
    public const EQ = 'eq';
    public const NEQ = 'neq';
    public const GT = 'gt';
    public const GTE = 'gte';
    public const ILIKE = 'ilike';
    public const BETWEEN = 'between';
    public const IN = 'in';
    public const NOT_IN = 'not_in';
    public const IS_NULL = 'is_null';
    public const IS_NOT_NULL = 'is_not_null';
    public const LIKE = 'like';
    public const NOT_LIKE = 'not_like';
    public const LT = 'lt';
    public const LTE = 'lte';
    public const ORDER_BY = 'order_by';
    public const DATE_BETWEEN = 'date_between';
    public const DATE_EQ = 'date_eq';

    public const SUPPORTED_EXPRESSIONS = [
        self::EQ,
        self::NEQ,
        self::GT,
        self::GTE,
        self::ILIKE,
        self::BETWEEN,
        self::IN,
        self::NOT_IN,
        self::IS_NULL,
        self::IS_NOT_NULL,
        self::LIKE,
        self::NOT_LIKE,
        self::LT,
        self::LTE,
        self::ORDER_BY,
        self::DATE_BETWEEN,
        self::DATE_EQ,
    ];

    public function __construct(private readonly QueryBuilder $qb)
    {
    }

    public function andWhere(Field $field): void
    {
        $this->{$field->getExprMethod()}($field); /* @phpstan-ignore-line */
    }

    private function andWhereAndX(Field $field): void
    {
        $this->andWhereComposite($field, CompositeExpression::TYPE_AND);
    }

    /**
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    private function andWhereComposite(Field $field, string $type): void
    {
        Assert::inArray($type, [CompositeExpression::TYPE_AND, CompositeExpression::TYPE_OR]);
        $parts = [];
        /**
         * @var int   $i
         * @var mixed $value
         */
        foreach ($field->getValues() as $i => $value) {
            /** @var string $sql */
            $sql = $this->qb->expr()->{$field->getExprMethod()}(/* @phpstan-ignore-line */
                $field->getPropertyPath(),
                $field->generateSQLParameter($i)
            );

            $parts[] = $sql;
            if (true === $field->isLike()) {
                $this->qb->setParameter($field->generateParameter($i), "%{$value}%");

                continue;
            }

            $this->qb->setParameter($field->generateParameter($i), $value);
        }

        $this->qb->andWhere(new CompositeExpression($type, $parts));
    }

    private function andWhereOrX(Field $field): void
    {
        $this->andWhereComposite($field, CompositeExpression::TYPE_OR);
    }

    private function between(Field $field): void
    {
        Assert::eq($field->countValues(), 2, 'Invalid format for between, expected "min|max"');
        [$min, $max] = $field->getValues();
        Assert::lessThan($min, $max, 'Invalid values for between, expected min < max');

        //phpcs:disable
        $this->qb->andWhere("{$field->getPropertyPath()} BETWEEN {$field->generateSQLParameter('from')} AND {$field->generateSQLParameter('to')}")
            //phpcs:enable
            ->setParameter($field->generateParameter('from'), $min)
            ->setParameter($field->generateParameter('to'), $max);
    }

    private function eq(Field $field): void
    {
        $this->andWhereOrX($field);
    }

    private function gt(Field $field): void
    {
        Assert::eq($field->countValues(), 1, 'expected single value');
        $this->qb->andWhere($this->qb->expr()->gt($field->getPropertyPath(), $field->generateSQLParameter('gt')))
            ->setParameter($field->generateParameter('gt'), $field->getValues()[0]);
    }

    private function gte(Field $field): void
    {
        Assert::eq($field->countValues(), 1, 'expected single value');
        $this->qb->andWhere($this->qb->expr()->gte($field->getPropertyPath(), $field->generateSQLParameter('gte')))
            ->setParameter($field->generateParameter('gte'), $field->getValues()[0]);
    }

    private function ilike(Field $field): void
    {
        $parts = [];
        /**
         * @var int   $i
         * @var mixed $value
         */
        foreach ($field->getValues() as $i => $value) {
            $parts[] = $this->qb->expr()->like(
                "LOWER({$field->getPropertyPath()})",
                "LOWER({$field->generateSQLParameter($i)})"
            );

            $this->qb->setParameter($field->generateParameter($i), mb_strtolower("%{$value}%"));
        }

        $this->qb->andWhere(new CompositeExpression(CompositeExpression::TYPE_OR, $parts));
    }

    private function in(Field $field): void
    {
        Assert::notEmpty($field->countValues(), 'expression "in" expected not empty value.');

        $this->qb->andWhere($this->qb->expr()->in($field->getPropertyPath(), $field->generateSQLParameter('in')))
            ->setParameter(
                $field->generateParameter('in'),
                $field->getValues(),
                \is_string($field->getValues()[0])
                    ? Connection::PARAM_STR_ARRAY
                    : Connection::PARAM_INT_ARRAY
            );
    }

    private function isNotNull(Field $field): void
    {
        $this->qb->andWhere($this->qb->expr()->isNotNull($field->getPropertyPath()));
    }

    private function isNull(Field $field): void
    {
        $this->qb->andWhere($this->qb->expr()->isNull($field->getPropertyPath()));
    }

    private function like(Field $field): void
    {
        $this->andWhereOrX($field);
    }

    private function lt(Field $field): void
    {
        Assert::eq($field->countValues(), 1, 'expected single value');
        $this->qb->andWhere($this->qb->expr()->lt($field->getPropertyPath(), $field->generateSQLParameter('lt')))
            ->setParameter($field->generateParameter('lt'), $field->getValues()[0]);
    }

    private function lte(Field $field): void
    {
        Assert::eq($field->countValues(), 1, 'expected single value');
        $this->qb->andWhere($this->qb->expr()->lte($field->getPropertyPath(), $field->generateSQLParameter('lte')))
            ->setParameter($field->generateParameter('lte'), $field->getValues()[0]);
    }

    private function neq(Field $field): void
    {
        $this->andWhereAndX($field);
    }

    private function notIn(Field $field): void
    {
        Assert::notEmpty($field->countValues(), 'expression "not_in" expected not empty value.');

        $this->qb->andWhere($this->qb->expr()->notIn($field->getPropertyPath(), $field->generateSQLParameter('not_in')))
            ->setParameter(
                $field->generateParameter('not_in'),
                $field->getValues(),
                \is_string($field->getValues()[0])
                    ? Connection::PARAM_STR_ARRAY
                    : Connection::PARAM_INT_ARRAY
            );
    }

    private function notLike(Field $field): void
    {
        $this->andWhereAndX($field);
    }

    private function orderBy(Field $field): void
    {
        Assert::eq($field->countValues(), 1, 'expected single value');
        $order = $field->getValues()[0];
        Assert::true(\is_string($order));
        $order = mb_strtolower($order);
        Assert::oneOf($order, ['asc', 'desc', 'asc nulls last', 'desc nulls last']);
        $this->qb->addOrderBy($field->getPropertyPath(), (string) $field->getValues()[0]);
    }

    private function dateBetween(Field $field): void
    {
        Assert::eq($field->countValues(), 2, 'Invalid format for between, expected "min|max"');
        /**
         * @var string $min
         * @var string $max
         */
        [$min, $max] = $field->getValues();

        foreach ([$min, $max] as $date) {
            $this->accessRegexDate($date);
        }

        Assert::lessThan($min, $max, 'Invalid values for between, expected min < max');
        //phpcs:disable
        $this->qb->andWhere("{$field->getPropertyPath()} BETWEEN {$field->generateSQLParameter('from')} AND {$field->generateSQLParameter('to')}")
            //phpcs:enable
            ->setParameter($field->generateParameter('from'), new \DateTime($min), Types::DATETIME_MUTABLE)
            ->setParameter(
                $field->generateParameter('to'),
                new \DateTime(sprintf('%s 23:59:59', $max)),
                Types::DATETIME_MUTABLE
            );
    }

    private function dateEq(Field $field): void
    {
        Assert::eq($field->countValues(), 1, 'expected single value');
        /** @var string $date */
        $date = $field->getValues()[0];
        $this->accessRegexDate($date);
        $this->qb->andWhere($this->qb->expr()->gte($field->getPropertyPath(), $field->generateSQLParameter('date')))
            ->setParameter($field->generateParameter('date'), new \DateTime($date), Types::DATETIME_MUTABLE);
    }

    private function accessRegexDate(string $date): void
    {
        //phpcs:disable
        Assert::regex($date, '/^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)(?:0?2)\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/');
        //phpcs:enable
    }
}