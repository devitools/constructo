<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver\FromDefaultValue;
use Constructo\Support\Set;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class FromDefaultValueTest extends TestCase
{
    public function testShouldResolveParameterWithDefaultValue(): void
    {
        $resolver = new FromDefaultValue();
        $method = new ReflectionMethod($this, 'methodWithDefaultValue');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertEquals('default', $result->get());
    }

    public function testShouldResolveNullableParameter(): void
    {
        $resolver = new FromDefaultValue();
        $method = new ReflectionMethod($this, 'methodWithNullableParameter');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertNull($result->get());
    }

    public function testShouldReturnNullForRequiredParameter(): void
    {
        $resolver = new FromDefaultValue();
        $method = new ReflectionMethod($this, 'methodWithRequiredParameter');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNull($result);
    }

    private function methodWithDefaultValue(string $value = 'default'): void
    {
    }

    private function methodWithNullableParameter(?string $value): void
    {
    }

    private function methodWithRequiredParameter(string $value): void
    {
    }
}
