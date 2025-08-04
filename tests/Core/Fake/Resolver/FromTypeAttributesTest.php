<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver\FromTypeAttributes;
use Constructo\Support\Set;
use Constructo\Test\Stub\AttributesVariety;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class FromTypeAttributesTest extends TestCase
{
    public function testShouldResolveManagedAttribute(): void
    {
        $resolver = new FromTypeAttributes();
        $reflection = new ReflectionClass(AttributesVariety::class);
        $parameter = $reflection->getConstructor()->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertIsString($result->get());
    }

    public function testShouldResolveDefineAttribute(): void
    {
        $resolver = new FromTypeAttributes();
        $reflection = new ReflectionClass(AttributesVariety::class);
        $parameter = $reflection->getConstructor()->getParameters()[2];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertIsString($result->get());
        $this->assertStringContainsString('@', $result->get());
    }

    public function testShouldResolvePatternAttribute(): void
    {
        $resolver = new FromTypeAttributes();
        $reflection = new ReflectionClass(AttributesVariety::class);
        $parameter = $reflection->getConstructor()->getParameters()[3];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertIsString($result->get());
        $this->assertMatchesRegularExpression('/^[A-Z0-9]+$/', $result->get());
    }

    public function testShouldReturnNullForParameterWithoutAttributes(): void
    {
        $resolver = new FromTypeAttributes();
        $reflection = new ReflectionClass(AttributesVariety::class);
        $parameter = $reflection->getConstructor()->getParameters()[9];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNull($result);
    }
}
