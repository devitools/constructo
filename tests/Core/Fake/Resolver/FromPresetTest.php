<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver\FromPreset;
use Constructo\Support\Set;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class FromPresetTest extends TestCase
{
    public function testShouldResolveFromPresetValue(): void
    {
        $resolver = new FromPreset();
        $method = new ReflectionMethod($this, 'methodWithParameter');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom(['name' => 'preset_value']);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertEquals('preset_value', $result->get());
    }

    public function testShouldReturnNullWhenPresetNotFound(): void
    {
        $resolver = new FromPreset();
        $method = new ReflectionMethod($this, 'methodWithParameter');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom(['other' => 'value']);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNull($result);
    }

    public function testShouldReturnNullWithEmptyPresets(): void
    {
        $resolver = new FromPreset();
        $method = new ReflectionMethod($this, 'methodWithParameter');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNull($result);
    }

    private function methodWithParameter(string $name): void
    {
    }
}
