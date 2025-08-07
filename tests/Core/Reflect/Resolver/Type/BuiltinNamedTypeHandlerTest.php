<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Reflect\Resolver\Type;

use Constructo\Core\Reflect\Resolver\Type\BuiltinNamedTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\Contract\NamedTypeResolution;
use Constructo\Core\Serialize\Builder;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use Constructo\Testing\MakeExtension;
use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class BuiltinNamedTypeHandlerTest extends TestCase
{
    use MakeExtension;

    private BuiltinNamedTypeHandler $handler;
    private Specs $specs;

    protected function setUp(): void
    {
        $this->handler = $this->make(BuiltinNamedTypeHandler::class);
        $builder = $this->make(Builder::class);
        $specsData = [
            'string' => [],
            'integer' => [],
            'numeric' => [],
            'boolean' => [],
            'array' => [],
        ];

        $specsFactory = new DefaultSpecsFactory($builder, $specsData);
        $this->specs = $specsFactory->make();
    }

    public function testResolveBuiltinStringType(): void
    {
        $parameter = $this->createParameterWithType('string', true);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('string'));
    }

    public function testResolveBuiltinIntType(): void
    {
        $parameter = $this->createParameterWithType('int', true);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('integer'));
    }

    public function testResolveBuiltinFloatType(): void
    {
        $parameter = $this->createParameterWithType('float', true);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('numeric'));
    }

    public function testResolveBuiltinBoolType(): void
    {
        $parameter = $this->createParameterWithType('bool', true);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('boolean'));
    }

    public function testResolveBuiltinArrayType(): void
    {
        $parameter = $this->createParameterWithType('array', true);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('array'));
    }

    public function testDoesNotResolveNonBuiltinType(): void
    {
        $parameter = $this->createParameterWithType('stdClass', false);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('string'));
        $this->assertFalse($field->hasRule('array'));
    }

    public function testDoesNotResolveUnionType(): void
    {
        $parameter = $this->createParameterWithUnionType();
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('string'));
        $this->assertFalse($field->hasRule('integer'));
        $this->assertFalse($field->hasRule('array'));
    }

    private function createParameterWithType(string $typeName, bool $isBuiltin): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);
        $type = $this->createMock(ReflectionNamedType::class);

        $type->method('getName')->willReturn($typeName);
        $type->method('isBuiltin')->willReturn($isBuiltin);

        $parameter->method('getType')->willReturn($type);

        return $parameter;
    }

    private function createParameterWithUnionType(): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);
        $unionType = $this->createMock(ReflectionUnionType::class);

        $parameter->method('getType')->willReturn($unionType);

        return $parameter;
    }
}
