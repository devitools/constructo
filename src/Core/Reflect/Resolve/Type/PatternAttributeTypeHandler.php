<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Resolve\Type;

use Constructo\Core\Reflect\Resolve\Type\Contract\TypeHandler;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Reflective\Attribute\Pattern;
use ReflectionAttribute;
use ReflectionParameter;

use function array_shift;
use function assert;

class PatternAttributeTypeHandler extends TypeHandler
{
    public function resolve(ReflectionParameter $parameter, Field $field): void
    {
        $this->resolvePatternAttribute($parameter, $field);

        parent::resolve($parameter, $field);
    }

    private function resolvePatternAttribute(ReflectionParameter $parameter, Field $field): void
    {
        $attributes = $parameter->getAttributes(Pattern::class);
        if (empty($attributes)) {
            return;
        }

        $attribute = array_shift($attributes);
        assert($attribute instanceof ReflectionAttribute);
        $instance = $attribute->newInstance();
        assert($instance instanceof Pattern);

        $field->regex($instance->pattern);
    }
}
