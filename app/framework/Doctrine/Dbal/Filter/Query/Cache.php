<?php

declare(strict_types=1);

namespace App\Framework\Doctrine\Dbal\Filter\Query;

use SimpleCache;

final class Cache
{
    public function __construct(private readonly SimpleCache $cacheBridge)
    {
    }

    public function generateCacheKeys(string $key, string $query, array $params = []): array
    {
        $realCacheKey = $key . 'query=' . $query . '&params=' . hash('sha256', serialize($params));

        return [sha1($realCacheKey), $realCacheKey];
    }

    public function get(string $cacheKey): mixed
    {
        return $this->cacheBridge->get($cacheKey);
    }

    public function set(string $cacheKey, mixed $data, int $lifeTime = 0): bool
    {
        return $this->cacheBridge->set($cacheKey, $data, $lifeTime);
    }
}