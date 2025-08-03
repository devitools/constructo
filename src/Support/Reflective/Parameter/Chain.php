<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Parameter;

use Constructo\Core\Metadata\Schema\Element\SchemaRegistry;
use Constructo\Core\Metadata\Schema\Field;
use ReflectionParameter;

abstract class Chain
{
    protected ?Chain $previous = null;

    public function __construct(protected readonly ?SchemaRegistry $specs = null)
    {
    }

    public function resolve(ReflectionParameter $parameter, Field $field, array $path): void
    {
        if (isset($this->previous)) {
            $this->previous->resolve($parameter, $field, $path);
        }
    }

    final public function then(Chain $resolver): Chain
    {
        $resolver->setPrevious($this);
        return $resolver;
    }

    final protected function setPrevious(Chain $previous): void
    {
        $this->previous = $previous;
    }
}
