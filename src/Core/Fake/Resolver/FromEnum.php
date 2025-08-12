<?php

declare(strict_types=1);

namespace Constructo\Core\Fake\Resolver;

use BackedEnum;
use Constructo\Core\Fake\Resolver;
use Constructo\Support\Set;
use Constructo\Support\Value;
use Random\RandomException;
use ReflectionEnum;
use ReflectionEnumUnitCase;
use ReflectionNamedType;
use ReflectionParameter;

final class FromEnum extends Resolver
{
    /**
     * @throws RandomException
     */
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        $type = $parameter->getType();
        if (! $type instanceof ReflectionNamedType) {
            return parent::resolve($parameter, $presets);
        }
        $enum = $type->getName();
        if (! enum_exists($enum)) {
            return parent::resolve($parameter, $presets);
        }
        $reflectionEnum = new ReflectionEnum($enum);
        return $this->resolveEnumValue($reflectionEnum, $parameter, $presets);
    }

    /**
     * @throws RandomException
     */
    private function resolveEnumValue(ReflectionEnum $reflectionEnum, ReflectionParameter $parameter, Set $presets): ?Value
    {
        /** @var ReflectionEnumUnitCase[] $enumCases */
        $enumCases = $reflectionEnum->getCases();
        if (empty($enumCases)) {
            return parent::resolve($parameter, $presets);
        }

        $case = $enumCases[random_int(0, count($enumCases) - 1)]->getValue();
        if ($case instanceof BackedEnum) {
            return new Value($case->value);
        }
        return new Value($case);
    }
}
