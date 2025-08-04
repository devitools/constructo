<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Schema\Parameter\Field\Contract;

use Constructo\Core\Metadata\Schema\Field;
use Constructo\Support\Reflective\Schema\Parameter\Registry\Types;
use ReflectionParameter;

abstract class TypeHandler
{
    protected ?TypeHandler $previous = null;

    public function __construct(protected readonly ?Types $types = null)
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
