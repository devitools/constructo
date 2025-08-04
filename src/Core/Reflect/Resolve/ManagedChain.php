<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Resolve;

use Constructo\Core\Reflect\Chain;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Reflective\Attribute\Managed;
use ReflectionParameter;

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
