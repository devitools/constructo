<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver\FromEnum;
use Constructo\Support\Set;
use Constructo\Test\Stub\Type\Enumeration;
use Constructo\Test\Stub\Type\Gender;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class FromEnumTest extends TestCase
{
    public function testShouldResolveBackedEnumParameter(): void
    {
        $resolver = new FromEnum();
        $method = new ReflectionMethod($this, 'methodWithBackedEnum');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertContains($result->get(), ['male', 'female']);
    }

    public function testShouldReturnNullForNonBackedEnum(): void
    {
        $resolver = new FromEnum();
        $method = new ReflectionMethod($this, 'methodWithNonBackedEnum');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNull($result);
    }

    public function testShouldReturnNullForNonEnumParameter(): void
    {
        $resolver = new FromEnum();
        $method = new ReflectionMethod($this, 'methodWithNonEnum');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNull($result);
    }

    private function methodWithBackedEnum(Gender $gender): void
    {
    }

    private function methodWithNonBackedEnum(Enumeration $enum): void
    {
    }

    private function methodWithNonEnum(string $value): void
    {
    }
}
