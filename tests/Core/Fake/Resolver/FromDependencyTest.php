<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver\FromDependency;
use Constructo\Support\Set;
use Constructo\Test\Stub\Domain\Entity\Game;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class FromDependencyTest extends TestCase
{
    public function testShouldResolveClassDependency(): void
    {
        $resolver = new FromDependency();
        $method = new ReflectionMethod($this, 'methodWithClassDependency');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertIsArray($result->get());
    }

    public function testShouldReturnNullForBuiltinType(): void
    {
        $resolver = new FromDependency();
        $method = new ReflectionMethod($this, 'methodWithBuiltinType');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNull($result);
    }

    public function testShouldReturnNullForIntersectionType(): void
    {
        $resolver = new FromDependency();
        $method = new ReflectionMethod($this, 'methodWithIntersectionType');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNull($result);
    }

    private function methodWithClassDependency(Game $game): void
    {
    }

    private function methodWithBuiltinType(string $value): void
    {
    }

    private function methodWithIntersectionType(\Countable&\Iterator $value): void
    {
    }
}
