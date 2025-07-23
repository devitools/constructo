<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Factory;

use Constructo\Support\Reflective\Ruleset;
use ReflectionParameter;

abstract class Chain extends Ruler
{
    protected ?Chain $previous = null;

    public function resolve(ReflectionParameter $parameter, Ruleset $rules): Ruleset
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($parameter, $rules);
        }
        return $rules;
    }

    final public function then(Chain $resolver): Chain
    {
        $resolver->previous($this);
        return $resolver;
    }

    final protected function previous(Chain $previous): void
    {
        $this->previous = $previous;
    }
}
