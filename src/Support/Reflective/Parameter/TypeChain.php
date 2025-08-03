<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Parameter;

use Constructo\Core\Metadata\Schema\Field;
use Constructo\Support\Reflective\Parameter\Type\BuiltinNamedTypeHandler;
use Constructo\Support\Reflective\Parameter\Type\DefineAttributeTypeHandler;
use Constructo\Support\Reflective\Parameter\Type\DependencyTypeHandler;
use Constructo\Support\Reflective\Parameter\Type\EnumNamedTypeHandler;
use Constructo\Support\Reflective\Parameter\Type\PatternAttributeTypeHandler;
use ReflectionParameter;

class TypeChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, Field $field, array $path): void
    {
        (new DependencyTypeHandler($this->specs))
            ->then(new EnumNamedTypeHandler())
            ->then(new BuiltinNamedTypeHandler())
            ->then(new DefineAttributeTypeHandler())
            ->then(new PatternAttributeTypeHandler())
            ->resolve($parameter, $field);

        parent::resolve($parameter, $field, $path);
    }
}
