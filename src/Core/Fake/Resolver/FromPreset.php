<?php

declare(strict_types=1);

namespace Constructo\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver;
use Constructo\Support\Set;
use Constructo\Support\Value;
use ReflectionParameter;

final class FromPreset extends Resolver
{
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        $field = $this->casedField($parameter);
        if ($presets->has($field)) {
            return new Value($presets->get($field));
        }
        return parent::resolve($parameter, $presets);
    }
}
