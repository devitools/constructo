<?php

declare(strict_types=1);

namespace Morph\Core\Serialize;

use Morph\Support\Set;
use Morph\Support\Value;
use ReflectionParameter;

abstract class Resolver extends Builder
{
    protected ?Resolver $previous = null;

    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($parameter, $set);
        }
        return $this->notResolvedAsInvalid($set->get($this->casedField($parameter)));
    }

    final public function then(Resolver $resolver): Resolver
    {
        $resolver->previous($this);
        return $resolver;
    }

    protected function previous(Resolver $previous): void
    {
        $this->previous = $previous;
    }
}
