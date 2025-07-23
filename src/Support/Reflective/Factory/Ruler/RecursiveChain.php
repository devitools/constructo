<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Factory\Ruler;

use Constructo\Support\Reflective\Factory\Chain;
use Constructo\Support\Reflective\Ruleset;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

class RecursiveChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Ruleset $rules): Ruleset
    {
        $path = [$this->casedField($parameter)];
        $this->resolveRecursive($parameter->getType(), $rules, $path);
        return parent::resolve($parameter, $rules);
    }

    /**
     * @param array<string> $path
     * @throws ReflectionException
     */
    private function resolveRecursive(?ReflectionType $type, Ruleset $rules, array $path): bool
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $this->resolveRecursiveNamedType($type, $rules, $path),
            $type instanceof ReflectionUnionType => $this->resolveRecursiveUnionType($type, $rules, $path),
            default => false,
        };
    }

    /**
     * @param array<string> $path
     * @throws ReflectionException
     */
    private function resolveRecursiveNamedType(ReflectionNamedType $type, Ruleset $rules, array $path): bool
    {
        $name = $type->getName();
        if (isset($this->native[$name]) || ! class_exists($name)) {
            return false;
        }
        $this->make($name, $rules, $path);
        return true;
    }

    /**
     * @param array<string> $path
     * @throws ReflectionException
     */
    private function resolveRecursiveUnionType(ReflectionUnionType $type, Ruleset $rules, array $path): bool
    {
        $types = $type->getTypes();
        foreach ($types as $subType) {
            $done = $this->resolveRecursive($subType, $rules, $path);
            if ($done) {
                break;
            }
        }
        return $done ?? false;
    }
}
