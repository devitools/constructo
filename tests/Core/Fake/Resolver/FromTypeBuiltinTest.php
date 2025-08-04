<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver\FromTypeBuiltin;
use Constructo\Support\Set;
use Constructo\Test\Stub\Builtin;
use Constructo\Test\Stub\Domain\Entity\Game;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

final class FromTypeBuiltinTest extends TestCase
{
    public function testShouldResolveStringType(): void
    {
        $resolver = new FromTypeBuiltin();
        $reflection = new ReflectionClass(Builtin::class);
        $parameter = $reflection->getConstructor()->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertIsString($result->get());
    }

    public function testShouldResolveIntType(): void
    {
        $resolver = new FromTypeBuiltin();
        $reflection = new ReflectionClass(Builtin::class);
        $parameter = $reflection->getConstructor()->getParameters()[1];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertIsInt($result->get());
        $this->assertGreaterThanOrEqual(1, $result->get());
        $this->assertLessThanOrEqual(100, $result->get());
    }

    public function testShouldResolveFloatType(): void
    {
        $resolver = new FromTypeBuiltin();
        $reflection = new ReflectionClass(Builtin::class);
        $parameter = $reflection->getConstructor()->getParameters()[2];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertIsFloat($result->get());
    }

    public function testShouldResolveBoolType(): void
    {
        $resolver = new FromTypeBuiltin();
        $reflection = new ReflectionClass(Builtin::class);
        $parameter = $reflection->getConstructor()->getParameters()[3];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertIsBool($result->get());
    }

    public function testShouldResolveArrayType(): void
    {
        $resolver = new FromTypeBuiltin();
        $reflection = new ReflectionClass(Builtin::class);
        $parameter = $reflection->getConstructor()->getParameters()[4];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertIsArray($result->get());
    }

    public function testShouldReturnNullForNonBuiltinType(): void
    {
        $resolver = new FromTypeBuiltin();
        $method = new ReflectionMethod($this, 'methodWithNonBuiltinType');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNull($result);
    }

    private function methodWithNonBuiltinType(Game $game): void
    {
    }
}
