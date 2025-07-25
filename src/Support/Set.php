<?php

declare(strict_types=1);

namespace Constructo\Support;

use Constructo\Exception\SchemaException;

use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function count;
use function is_array;

readonly class Set
{
    /**
     * @var array<string, mixed>
     */
    private array $data;

    public function __construct(mixed $data = [])
    {
        if (! is_array($data)) {
            throw new SchemaException('Values must be an array.');
        }
        $keys = array_keys($data);
        $filtered = array_filter($keys, 'is_string');
        if (count($keys) !== count($filtered)) {
            throw new SchemaException('All keys must be strings.');
        }
        /* @phpstan-ignore assign.propertyType */
        $this->data = $data;
    }

    public static function createFrom(mixed $data): static
    {
        return new static($data);
    }

    public function get(string $field, mixed $default = null): mixed
    {
        return $this->data[$field] ?? $default;
    }

    public function at(string $field): mixed
    {
        if (array_key_exists($field, $this->data)) {
            return $this->data[$field];
        }
        throw new SchemaException(sprintf("Field '%s' not found.", $field));
    }

    public function with(string $field, mixed $value): static
    {
        return new static(array_merge($this->toArray(), [$field => $value]));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        /* @phpstan-ignore return.type */
        return array_map(fn (mixed $item) => $item instanceof Set ? $item->toArray() : $item, $this->data);
    }

    public function along(array $values): static
    {
        return new static(array_merge($this->toArray(), $values));
    }

    public function has(string $field): bool
    {
        return array_key_exists($field, $this->data);
    }

    public function hasNot(string $field): bool
    {
        return ! $this->has($field);
    }
}
