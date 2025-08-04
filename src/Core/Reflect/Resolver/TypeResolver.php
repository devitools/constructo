<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Resolver;

use Constructo\Core\Reflect\Resolver;
use Constructo\Core\Reflect\Resolver\Type\BuiltinNamedTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\DefineAttributeTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\DependencyTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\EnumNamedTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\PatternAttributeTypeHandler;
use Constructo\Support\Metadata\Schema\Field;
use ReflectionParameter;

class TypeResolver extends Resolver
{
    public function resolve(ReflectionParameter $parameter, Field $field, array $path): void
    {
        (new DependencyTypeHandler($this->types))
            ->then(new EnumNamedTypeHandler())
            ->then(new BuiltinNamedTypeHandler())
            ->then(new DefineAttributeTypeHandler())
            ->then(new PatternAttributeTypeHandler())
            ->resolve($parameter, $field);

        parent::resolve($parameter, $field, $path);
    }
}
