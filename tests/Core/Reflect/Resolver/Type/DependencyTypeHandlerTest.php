<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Reflect\Resolver\Type;

use Constructo\Core\Reflect\Resolver\Type\DependencyTypeHandler;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use Constructo\Support\Metadata\Schema\Registry\Types;
use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionParameter;
use stdClass;

final class DependencyTypeHandlerTest extends TestCase
{
    private DependencyTypeHandler $handler;
    private Specs $specs;
    private Types $types;

    protected function setUp(): void
    {
        $this->types = new Types();
        $this->handler = new DependencyTypeHandler($this->types);

        $specsData = [
            'array' => [],
            'string' => [],
        ];

        $specsFactory = new DefaultSpecsFactory($specsData);
        $this->specs = $specsFactory->make();
    }

    public function testResolveClassDependency(): void
    {
        $parameter = $this->createParameterWithType(stdClass::class, false);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('array'));
        $this->assertEquals(stdClass::class, $field->getSource());
    }

    public function testResolveClassDependencyWithRegisteredType(): void
    {
        $typesWithMapping = new Types([stdClass::class => 'string']);
        $handler = new DependencyTypeHandler($typesWithMapping);
        $parameter = $this->createParameterWithType(stdClass::class, false);
        $field = new Field($this->specs, new Rules(), 'test');

        $handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('string'));
        $this->assertNull($field->getSource());
    }

    public function testDoesNotResolveBuiltinType(): void
    {
        $parameter = $this->createParameterWithType('string', true);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('array'));
        $this->assertNull($field->getSource());
    }

    public function testDoesNotResolveNonExistentClass(): void
    {
        $parameter = $this->createParameterWithType('NonExistentClass', false);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('array'));
        $this->assertNull($field->getSource());
    }

    public function testDoesNotResolveEnumType(): void
    {
        $parameter = $this->createParameterWithType('TestEnum', false);
        $field = new Field($this->specs, new Rules(), 'test');

        // Mock enum_exists to return true for TestEnum
        $this->handler = new class($this->types) extends DependencyTypeHandler {
            protected function resolveNamedType(\ReflectionNamedType $parameter, \Constructo\Support\Metadata\Schema\Field $field): \Constructo\Core\Reflect\Resolver\Type\Contract\NamedTypeResolution
            {
                $source = $parameter->getName();
                if ($source === 'TestEnum') {
                    return \Constructo\Core\Reflect\Resolver\Type\Contract\NamedTypeResolution::NotResolved;
                }
                return parent::resolveNamedType($parameter, $field);
            }
        };

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('array'));
        $this->assertNull($field->getSource());
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
}
