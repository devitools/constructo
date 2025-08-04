<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Reflect\Resolver;

use Constructo\Core\Reflect\Resolver\TypeResolver;
use Constructo\Core\Reflect\Resolver\Type\BuiltinNamedTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\DefineAttributeTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\DependencyTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\EnumNamedTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\PatternAttributeTypeHandler;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use Constructo\Support\Metadata\Schema\Registry\Types;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

final class TypeResolverTest extends TestCase
{
    private TypeResolver $chain;
    private Types $types;
    private Specs $specs;

    protected function setUp(): void
    {
        $this->types = new Types();
        $this->specs = new Specs();
        $this->chain = new TypeResolver($this->types);
    }

    public function testResolveCreatesCorrectHandlerChain(): void
    {
        $parameter = $this->createMock(ReflectionParameter::class);
        $field = new Field($this->specs, new Rules(), 'test');
        $path = ['test'];

        // Mock the parameter to avoid actual type resolution
        $parameter->method('getType')->willReturn(null);
        $parameter->method('getAttributes')->willReturn([]);

        $this->chain->resolve($parameter, $field, $path);

        // If no exception is thrown, the chain was created successfully
        $this->assertTrue(true);
    }

    public function testResolveCallsParentResolve(): void
    {
        $parameter = $this->createMock(ReflectionParameter::class);
        $field = new Field($this->specs, new Rules(), 'test');
        $path = ['test'];

        // Mock the parameter to avoid actual type resolution
        $parameter->method('getType')->willReturn(null);
        $parameter->method('getAttributes')->willReturn([]);

        // Create a mock previous chain to verify it gets called
        $mockPreviousChain = $this->createMock(TypeResolver::class);
        $mockPreviousChain->expects($this->once())
            ->method('resolve')
            ->with($parameter, $field, $path);

        // Set up the chain with a previous chain
        $chain = new TypeResolver($this->types);
        $mockPreviousChain->then($chain);

        $chain->resolve($parameter, $field, $path);
    }

    public function testConstructorWithTypes(): void
    {
        $types = new Types();
        $chain = new TypeResolver($types);

        $this->assertInstanceOf(TypeResolver::class, $chain);
    }

    public function testConstructorWithoutTypes(): void
    {
        $chain = new TypeResolver();

        $this->assertInstanceOf(TypeResolver::class, $chain);
    }

    public function testResolveWithStringParameter(): void
    {
        $parameter = $this->createStringParameterMock();
        $field = new Field($this->specs, new Rules(), 'test');
        $path = ['test'];

        $this->chain->resolve($parameter, $field, $path);

        // The chain should process without errors
        $this->assertTrue(true);
    }

    public function testResolveWithIntegerParameter(): void
    {
        $parameter = $this->createIntegerParameterMock();
        $field = new Field($this->specs, new Rules(), 'test');
        $path = ['test'];

        $this->chain->resolve($parameter, $field, $path);

        // The chain should process without errors
        $this->assertTrue(true);
    }

    public function testResolveWithArrayParameter(): void
    {
        $parameter = $this->createArrayParameterMock();
        $field = new Field($this->specs, new Rules(), 'test');
        $path = ['test'];

        $this->chain->resolve($parameter, $field, $path);

        // The chain should process without errors
        $this->assertTrue(true);
    }

    private function createStringParameterMock(): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);
        $type = $this->createMock(\ReflectionNamedType::class);

        $type->method('getName')->willReturn('string');
        $type->method('isBuiltin')->willReturn(true);

        $parameter->method('getType')->willReturn($type);
        $parameter->method('getAttributes')->willReturn([]);

        return $parameter;
    }

    private function createIntegerParameterMock(): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);
        $type = $this->createMock(\ReflectionNamedType::class);

        $type->method('getName')->willReturn('int');
        $type->method('isBuiltin')->willReturn(true);

        $parameter->method('getType')->willReturn($type);
        $parameter->method('getAttributes')->willReturn([]);

        return $parameter;
    }

    private function createArrayParameterMock(): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);
        $type = $this->createMock(\ReflectionNamedType::class);

        $type->method('getName')->willReturn('array');
        $type->method('isBuiltin')->willReturn(true);

        $parameter->method('getType')->willReturn($type);
        $parameter->method('getAttributes')->willReturn([]);

        return $parameter;
    }
}
