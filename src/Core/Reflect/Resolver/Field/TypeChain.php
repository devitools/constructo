<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Resolver\Field;

use Constructo\Core\Reflect\Resolver\Type\BuiltinNamedTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\DefineAttributeTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\DependencyTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\EnumNamedTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\PatternAttributeTypeHandler;
use Constructo\Support\Metadata\Schema\Field;
use ReflectionParameter;

class TypeChain extends Chain
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
