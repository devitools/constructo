<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Schema\Parameter;

use Constructo\Core\Metadata\Schema\Field;
use Constructo\Support\Reflective\Schema\Parameter\Field\BuiltinNamedTypeHandler;
use Constructo\Support\Reflective\Schema\Parameter\Field\DefineAttributeTypeHandler;
use Constructo\Support\Reflective\Schema\Parameter\Field\DependencyTypeHandler;
use Constructo\Support\Reflective\Schema\Parameter\Field\EnumNamedTypeHandler;
use Constructo\Support\Reflective\Schema\Parameter\Field\PatternAttributeTypeHandler;
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
