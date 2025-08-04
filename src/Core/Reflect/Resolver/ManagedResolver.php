<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Resolver;

use Constructo\Core\Reflect\Resolver;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Reflective\Attribute\Managed;
use ReflectionParameter;

class ManagedResolver extends Resolver
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
