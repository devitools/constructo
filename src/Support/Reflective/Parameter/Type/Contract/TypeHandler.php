<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Parameter\Type\Contract;

use Constructo\Core\Metadata\Schema\Element\SchemaRegistry;
use Constructo\Core\Metadata\Schema\Field;
use ReflectionParameter;

abstract class TypeHandler
{
    protected ?TypeHandler $previous = null;

    public function __construct(protected readonly ?SchemaRegistry $specs = null)
    {
    }

    public function resolve(ReflectionParameter $parameter, Field $field): void
    {
        if (isset($this->previous)) {
            $this->previous->resolve($parameter, $field);
        }
    }

    final public function then(TypeHandler $resolver): TypeHandler
    {
        $resolver->setPrevious($this);
        return $resolver;
    }

    final protected function setPrevious(TypeHandler $previous): void
    {
        $this->previous = $previous;
    }
}
