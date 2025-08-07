<?php

declare(strict_types=1);

namespace Constructo\Support\Metadata\Schema\Field\Formatter;

use BadMethodCallException;
use Closure;
use Constructo\Contract\Formatter;

use function Constructo\Cast\arrayify;
use function Constructo\Cast\stringify;
use function count;
use function is_array;
use function vsprintf;

class PatternFormatter implements Formatter
{
    public function format(mixed $value, mixed $option = null): array
    {
        if (! is_array($value)) {
            throw new BadMethodCallException('PatternFormatter expects an array value.');
        }
        if (count($value) === 1) {
            return [stringify($value[0])];
        }
        if (count($value) === 2) {
            return $this->formatWithParameters($value);
        }
        throw new BadMethodCallException('PatternFormatter expects an array with one or two elements.');
    }

    public function formatWithParameters(array $value): array
    {
        [
            $pattern,
            $parameters,
        ] = $value;
        if ($parameters === null) {
            $parameters = [];
        }
        $parameters = $this->normalizeParameters($parameters);
        return [vsprintf(stringify($pattern), $parameters)];
    }

    /**
     * @return array<bool|float|int|string|null>
     */
    public function normalizeParameters(mixed $parameters): array
    {
        if ($parameters instanceof Closure) {
            $parameters = arrayify($parameters());
        }
        if (! is_array($parameters)) {
            $parameters = [$parameters];
        }

        $normalizer = fn (mixed $value) => match (true) {
            is_bool($value) => $value,
            is_int($value) => $value,
            is_float($value) => $value,
            is_string($value) => $value,
            default => null
        };
        return array_map($normalizer, $parameters);
    }
}
