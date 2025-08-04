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

    public function introspectable(): ?string
    {
        return (($this->type instanceof ReflectionNamedType) && ! $this->type->isBuiltin())
            ? $this->type->getName()
            : null;
    }
}
