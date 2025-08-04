<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Resolve;

use Constructo\Core\Reflect\Chain;
use Constructo\Core\Reflect\Resolve\Type\BuiltinNamedTypeHandler;
use Constructo\Core\Reflect\Resolve\Type\DefineAttributeTypeHandler;
use Constructo\Core\Reflect\Resolve\Type\DependencyTypeHandler;
use Constructo\Core\Reflect\Resolve\Type\EnumNamedTypeHandler;
use Constructo\Core\Reflect\Resolve\Type\PatternAttributeTypeHandler;
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
