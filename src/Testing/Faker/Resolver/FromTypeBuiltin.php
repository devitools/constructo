<?php

declare(strict_types=1);

namespace Constructo\Testing\Faker\Resolver;

use Constructo\Support\Set;
use Constructo\Support\Value;
use ReflectionParameter;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Constructo\Testing\Extension\ManagedExtension;
use Constructo\Testing\Faker\Resolver;

final class FromTypeBuiltin extends Resolver
{
    use MakeExtension;
    use ManagedExtension;

    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        $type = $this->detectReflectionType($parameter->getType());
        if ($type === null) {
            return parent::resolve($parameter, $presets);
        }

        return $this->resolveByBuiltin($type)
            ?? parent::resolve($parameter, $presets);
    }

    private function resolveByBuiltin(string $type): ?Value
    {
        return match ($type) {
            'int' => new Value($this->generator->numberBetween(1, 100)),
            'string' => new Value($this->generator->word()),
            'bool' => new Value($this->generator->boolean()),
            'float' => new Value($this->generator->randomFloat(2, 1, 100)),
            'array' => new Value($this->generator->words()),
            default => null,
        };
    }
}
