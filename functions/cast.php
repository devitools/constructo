<?php

declare(strict_types=1);

namespace Morph\Cast;

use Stringable;

if (! function_exists(__NAMESPACE__ . '\arrayify')) {
    /**
     * @template T of array-key
     * @template U
     * @param array<T, U> $default
     * @return array<T, U>
     */
    function arrayify(mixed $value, array $default = []): array
    {
        return is_array($value) ? $value : $default;
    }
}

if (! function_exists(__NAMESPACE__ . '\mapify')) {
    /**
     * @param array<string, mixed> $default
     * @return array<string, mixed>
     */
    function mapify(mixed $data, array $default = []): array
    {
        $data = match (true) {
            is_object($data) => (array) $data,
            is_array($data) => $data,
            default => $default,
        };
        $mapping = [];
        foreach ($data as $key => $datum) {
            $key = is_string($key) ? $key : sprintf('key_%s', $key);
            $mapping[$key] = $datum;
        }
        return $mapping;
    }
}

if (! function_exists(__NAMESPACE__ . '\stringify')) {
    function stringify(mixed $value, string $default = ''): string
    {
        return match (true) {
            is_string($value) => $value,
            is_scalar($value) => (string) $value,
            (is_object($value) && method_exists($value, '__toString')) => (string) $value,
            $value instanceof Stringable => $value->__toString(),
            default => $default,
        };
    }
}

if (! function_exists(__NAMESPACE__ . '\integerify')) {
    function integerify(mixed $value, int $default = 0): int
    {
        $value = is_numeric($value) ? (int) $value : $value;
        return is_int($value) ? $value : $default;
    }
}

if (! function_exists(__NAMESPACE__ . '\floatify')) {
    function floatify(mixed $value, float $default = 0.0): float
    {
        $value = is_numeric($value) ? (float) $value : $value;
        return is_float($value) ? $value : $default;
    }
}

if (! function_exists(__NAMESPACE__ . '\boolify')) {
    function boolify(mixed $value, bool $default = false): bool
    {
        return is_bool($value) ? $value : $default;
    }
}
