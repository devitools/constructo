<?php

declare(strict_types=1);

namespace Constructo\Core\Deserialize\Resolve;

use DateTimeInterface;
use Constructo\Core\Deserialize\Chain;
use Constructo\Support\Value;
use Constructo\Type\Timestamp;
use ReflectionParameter;

class DateChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        $resolved = $this->resolveByClassName($value);
        if ($resolved) {
            return new Value($resolved);
        }
        return parent::resolve($parameter, $value);
    }

    private function resolveByClassName(mixed $value): ?string
    {
        return match (true) {
            $value instanceof Timestamp => $value->toString(),
            $value instanceof DateTimeInterface => $value->format(DateTimeInterface::ATOM),
            default => null,
        };
    }
}
