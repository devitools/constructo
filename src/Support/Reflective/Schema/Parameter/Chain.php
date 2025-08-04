<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Schema\Parameter;

use Constructo\Core\Metadata\Schema\Field;
use Constructo\Support\Reflective\Schema\Parameter\Registry\Types;
use ReflectionParameter;

abstract class Chain
{
    protected ?Chain $previous = null;

    public function __construct(protected readonly ?Types $types = null)
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
