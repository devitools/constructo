<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Introspection;

use ReflectionNamedType;
use ReflectionType;

readonly class Result
{
    public function __construct(
        public string $source,
        public ?ReflectionType $type = null,
    ) {
    }

    /**
     * @return class-string<object>|null
     */
    public function introspectable(): ?string
    {
        if ($this->type instanceof ReflectionNamedType && !$this->type->isBuiltin()) {
            /** @var class-string<object>|null */
            return $this->type->getName();
        }
        return null;
    }
}
