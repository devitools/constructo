<?php

declare(strict_types=1);

namespace Constructo\Support;

class Cache
{
    /**
     * @var array<string, mixed>
     */
    private array $cache = [];

    public function get(string $key): mixed
    {
        return $this->cache[$key] ?? null;
    }

    /**
     * @template T of mixed
     * @param T $value
     *
     * @return T
     */
    public function set(string $key, mixed $value): mixed
    {
        $this->cache[$key] = $value;
        return $value;
    }

    public function reset(): void
    {
        $this->cache = [];
    }
}
