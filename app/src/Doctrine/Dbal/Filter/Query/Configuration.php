<?php

declare(strict_types=1);

namespace App\Doctrine\Dbal\Filter\Query;

use App\DTO\AbstractCommand;
use App\Exception\InvalidTypeException;
use Webmozart\Assert\Assert;

class Configuration extends AbstractCommand
{
    private const ORDER_HAYSTACK = ['asc', 'desc', 'asc nulls last', 'desc nulls last'];
    public int $page = 1;
    public int $limit = 15;
    public bool $debug = false;
    public array $filter = [];
    public array $order = [];

    public function __construct(array $conditions)
    {
        parent::__construct($conditions);
        try {
            $filter = json_decode(
                json_encode(
                    $this->filter,
                    \JSON_THROW_ON_ERROR | \JSON_NUMERIC_CHECK + \JSON_PRESERVE_ZERO_FRACTION
                ),
                true,
                512,
                \JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            throw new InvalidTypeException('json', $e->getMessage());
        }

        $this->filter = $filter;
    }

    final public function setPage(string | int $page): void
    {
        $this->page = (int) $page;
    }

    final public function setLimit(string | int $limit): void
    {
        $this->limit = (int) $limit;
    }

    public function setFilter(array $filter): self
    {
        foreach ($filter as $exp => $values) {
            foreach ($values as $propertyAlias => $value) {
                if (!\array_key_exists($exp, $this->filter)) {
                    $this->filter[$exp] = [];
                }
                Assert::oneOf($exp, Builder::SUPPORTED_EXPRESSIONS);
                $this->filter[$exp][$propertyAlias] = explode('|', (string) $value);
            }
        }

        return $this;
    }

    public function addFilterExp(string $exp, string $alias, mixed $value): self
    {
        Assert::oneOf($exp, Builder::SUPPORTED_EXPRESSIONS);
        $this->filter[$exp][$alias] = explode('|', (string) $value);

        return $this;
    }

    public function setOrder(array $conditions): self
    {
        foreach ($conditions as $alias => $direction) {
            Assert::oneOf($direction, self::ORDER_HAYSTACK);
            $this->order[$alias] = $direction;
        }

        return $this;
    }

    final public function addOrderBy(string $alias, string $value): self
    {
        Assert::inArray($value, self::ORDER_HAYSTACK);
        $this->order[$alias] = $value;

        return $this;
    }
}