<?php

declare(strict_types=1);

namespace App\Framework\Security\Framework\Utils\Cache;

use Psr\SimpleCache\CacheInterface;
use InvalidArgumentException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException as CacheInvalidArgumentException;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use App\Framework\Exception\InvalidTypeException;


class SimpleCacheBridge implements CacheInterface
{
    protected MemcachedAdapter $cacheItemPool;

    public function __construct(\Memcached $memcached, string $namespace)
    {
        $this->cacheItemPool = new MemcachedAdapter($memcached, $namespace);
    }

    /**
     * @psalm-suppress InvalidScalarArgument
     */
    public function get($key, $default = null): mixed
    {
        try {
            $item = $this->cacheItemPool->getItem($key);
        } catch (CacheInvalidArgumentException $e) {
            throw new InvalidTypeException(CacheItemInterface::class, $e->getMessage(), $e->getCode());
        }

        if (!$item->isHit()) {
            return $default;
        }

        return $item->get();
    }

    /**
     * @psalm-suppress InvalidScalarArgument
     */
    public function set($key, $value, $ttl = null): bool
    {
        try {
            $item = $this->cacheItemPool->getItem($key);
            $item->expiresAfter($ttl);
        } catch (CacheInvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        $item->set($value);

        return $this->cacheItemPool->save($item);
    }

    public function delete($key): bool
    {
        try {
            return $this->cacheItemPool->deleteItem($key);
        } catch (CacheInvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function clear(): bool
    {
        return $this->cacheItemPool->clear();
    }

    public function getMultiple($keys, $default = null): iterable | \Generator
    {
        if (!\is_array($keys)) {
            // phpcs:disable
            if (!$keys instanceof \Traversable) { /* @phpstan-ignore-line */
                throw new InvalidArgumentException('$keys is neither an array nor Traversable');
            }
            // phpcs:enable

            $keys = iterator_to_array($keys, false);
        }

        try {
            $items = $this->cacheItemPool->getItems($keys);
        } catch (CacheInvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->generateValues($default, $items);
    }

    public function setMultiple($values, $ttl = null): bool
    {
        if (!is_iterable($values)) { /* @phpstan-ignore-line */
            throw new InvalidArgumentException('$values is neither an array nor Traversable');
        }

        $keys = [];
        $arrayValues = [];
        foreach ($values as $key => $value) {
            if (\is_int($key)) {
                $key = (string) $key;
            }

            if (!\is_string($key)) {
                throw new InvalidArgumentException(sprintf('Cache key must be string, "%s" given', \gettype($key)));
            }

            if (false !== preg_match('|[\{\}\(\)/\\\@\:]|', $key)) {
                // phpcs:disable
                throw new InvalidArgumentException(sprintf('Invalid key: "%s". The key contains one or more characters reserved for future extension: {}()/\@:', $key));
                // phpcs:enable
            }

            $keys[] = $key;
            $arrayValues[$key] = $value;
        }

        try {
            $items = $this->cacheItemPool->getItems($keys);
        } catch (CacheInvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        $itemSuccess = true;

        foreach ($items as $key => $item) {
            /* @var $item CacheItemInterface */
            $item->set($arrayValues[$key]);
            try {
                $item->expiresAfter($ttl);
            } catch (CacheInvalidArgumentException $e) {
                throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
            }
            $itemSuccess = $itemSuccess && $this->cacheItemPool->saveDeferred($item);
        }

        return $itemSuccess && $this->cacheItemPool->commit();
    }

    /**
     * @param \Traversable|array $keys
     */
    public function deleteMultiple($keys): bool
    {
        if (!\is_array($keys)) {
            $keys = iterator_to_array($keys, false);
        }

        try {
            return $this->cacheItemPool->deleteItems($keys);
        } catch (CacheInvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function has($key): bool
    {
        try {
            return $this->cacheItemPool->hasItem($key);
        } catch (CacheInvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function generateValues(mixed $default, array | \Traversable $items): \Generator
    {
        foreach ($items as $key => $item) {
            /** @var $item CacheItemInterface */
            if (!$item->isHit()) {
                yield $key => $default;
            } else {
                yield $key => $item->get();
            }
        }
    }
}