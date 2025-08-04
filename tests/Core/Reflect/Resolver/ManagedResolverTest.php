<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Reflect\Resolver;

use Constructo\Core\Reflect\Resolver\ManagedResolver;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use Constructo\Support\Reflective\Attribute\Managed;
use PHPUnit\Framework\TestCase;
use ReflectionAttribute;
use ReflectionParameter;

final class ManagedResolverTest extends TestCase
{
    private ManagedResolver $chain;
    private Specs $specs;

    protected function setUp(): void
    {
        $specsData = [];

        $specsFactory = new DefaultSpecsFactory($specsData);
        $this->specs = $specsFactory->make();
        $this->chain = new ManagedResolver();
    }

    public function testResolveWithManagedAttribute(): void
    {
        $parameter = $this->createParameterWithManagedAttribute();
        $field = new Field($this->specs, new Rules(), 'test');
        $path = ['test'];

        $this->chain->resolve($parameter, $field, $path);

        $this->assertFalse($field->isAvailable());
    }

    public function testResolveWithoutManagedAttribute(): void
    {
        $parameter = $this->createParameterWithoutManagedAttribute();
        $field = new Field($this->specs, new Rules(), 'test');
        $path = ['test'];

        $this->chain->resolve($parameter, $field, $path);

        $this->assertTrue($field->isAvailable());
    }

    public function testResolveCallsParentWhenNoManagedAttribute(): void
    {
        $parameter = $this->createParameterWithoutManagedAttribute();
        $field = new Field($this->specs, new Rules(), 'test');
        $path = ['test'];

        // Create a mock previous chain to verify it gets called
        $mockPreviousChain = $this->createMock(ManagedResolver::class);
        $mockPreviousChain->expects($this->once())
            ->method('resolve')
            ->with($parameter, $field, $path);

        // Set up the chain with a previous chain
        $chain = new ManagedResolver();
        $mockPreviousChain->then($chain);

        $chain->resolve($parameter, $field, $path);
    }

    private function createParameterWithManagedAttribute(): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);
        $reflectionAttribute = $this->createMock(ReflectionAttribute::class);
        $managedAttribute = new Managed('test-management');

        $reflectionAttribute->method('newInstance')->willReturn($managedAttribute);

        $parameter->method('getAttributes')
            ->with(Managed::class)
            ->willReturn([$reflectionAttribute]);

        return $parameter;
    }

    private function createParameterWithoutManagedAttribute(): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);

        $parameter->method('getAttributes')
            ->with(Managed::class)
            ->willReturn([]);

        return $parameter;
    }
}
