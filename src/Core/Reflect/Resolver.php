<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect;

use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Registry\Types;
use ReflectionParameter;

abstract class Resolver
{
    protected ?Resolver $previous = null;

    public function __construct(protected readonly ?Types $types = null)
    {
    }

    public function resolve(ReflectionParameter $parameter, Field $field, array $path): void
    {
        if (isset($this->previous)) {
            $this->previous->resolve($parameter, $field, $path);
        }
    }

    final public function then(Resolver $resolver): Resolver
    {
        $resolver->setPrevious($this);
        return $resolver;
    }

    final protected function setPrevious(Resolver $previous): void
    {
        $this->previous = $previous;
    }
}
