<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Parameter\Type;

use Constructo\Core\Metadata\Schema\Field;
use Constructo\Support\Reflective\Parameter\Type\Contract\TypeHandler;
use ReflectionAttribute;
use ReflectionParameter;
use Constructo\Support\Reflective\Attribute\Pattern;

use function array_shift;
use function assert;

class ManagedAttributeTypeHandler extends TypeHandler
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
