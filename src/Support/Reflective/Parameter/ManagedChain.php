<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Parameter;

use Constructo\Core\Metadata\Schema\Field;
use ReflectionParameter;
use Constructo\Support\Reflective\Attribute\Managed;

class ManagedChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, Field $field, array $path): void
    {
        $attributes = $parameter->getAttributes(Managed::class);
        if (! empty($attributes)) {
            $field->unavailable();
            return;
        }
        parent::resolve($parameter, $field, $path);
    }
}
