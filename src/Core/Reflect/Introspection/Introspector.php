<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Introspection;

use Iterator;
use ReflectionClass;
use ReflectionException;

class Introspector
{
    public function analyze(string $source): Result
    {
        try {
            $reflection = new ReflectionClass($source);

            if (! $reflection->implementsInterface(Iterator::class)) {
                return new Result($source);
            }

            $currentMethod = $reflection->getMethod('current');
            return new Result($source, $currentMethod->getReturnType());
        } catch (ReflectionException) {
            // If reflection fails, return null
        }
        return new Result($source);
    }
}
