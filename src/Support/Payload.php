<?php

declare(strict_types=1);

namespace Constructo\Support;

use Constructo\Exception\SchemaException;

use function is_array;

readonly class Payload extends Set
{
    public static function createFrom(mixed $data): self
    {
        return new self($data);
    }

    public function with(string $field, mixed $value): self
    {
        return new self(array_merge($this->toArray(), [$field => $value]));
    }

    public function along(array $values): self
    {
        return new self(array_merge($this->toArray(), $values));
    }

    public function __get(string $name): mixed
    {
        if (! $this->has($name)) {
            return null;
        }
        return $this->resolve($name);
    }

    public function __set(string $name, mixed $value): void
    {
        throw new SchemaException('Cannot modify payload properties');
    }

    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    protected function resolve(string $name): mixed
    {
        $value = $this->get($name);
        if (! is_array($value)) {
            return $value;
        }
        $keys = array_keys($value);
        $filtered = array_filter($keys, fn (mixed $item) => is_string($item));
        if (count($keys) !== count($filtered)) {
            return $value;
        }
        return new self($value);
    }
}
