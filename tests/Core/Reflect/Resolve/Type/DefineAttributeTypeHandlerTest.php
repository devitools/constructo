<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Reflect\Resolve\Type;

use Constructo\Core\Reflect\Resolve\Type\DefineAttributeTypeHandler;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use Constructo\Support\Reflective\Attribute\Define;
use Constructo\Support\Reflective\Definition\Type;
use Constructo\Support\Reflective\Definition\TypeExtended;
use PHPUnit\Framework\TestCase;
use ReflectionAttribute;
use ReflectionParameter;

final class DefineAttributeTypeHandlerTest extends TestCase
{
    private DefineAttributeTypeHandler $handler;
    private Specs $specs;

    protected function setUp(): void
    {
        $this->handler = new DefineAttributeTypeHandler();

        $specsData = [
            'string' => [],
            'integer' => [],
            'email' => [],
            'uuid' => [],
        ];

        $specsFactory = new DefaultSpecsFactory($specsData);
        $this->specs = $specsFactory->make();
    }

    public function testResolveDefineAttributeWithEmailType(): void
    {
        $defineAttribute = new Define(Type::EMAIL);
        $parameter = $this->createParameterWithDefineAttribute($defineAttribute);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('email'));
    }

    public function testResolveDefineAttributeWithUuidType(): void
    {
        $defineAttribute = new Define(Type::UUID);
        $parameter = $this->createParameterWithDefineAttribute($defineAttribute);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('uuid'));
    }

    public function testResolveDefineAttributeWithMultipleTypes(): void
    {
        $defineAttribute = new Define(Type::EMAIL, Type::UUID);
        $parameter = $this->createParameterWithDefineAttribute($defineAttribute);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('email'));
        $this->assertFalse($field->hasRule('uuid'));
    }

    public function testDoesNotResolveWhenTypeNotInSpecs(): void
    {
        $defineAttribute = new Define(Type::WORD);
        $parameter = $this->createParameterWithDefineAttribute($defineAttribute);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('word'));
        $this->assertFalse($field->hasRule('email'));
    }

    public function testDoesNotResolveWhenNoDefineAttribute(): void
    {
        $parameter = $this->createParameterWithoutDefineAttribute();
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('string'));
        $this->assertFalse($field->hasRule('email'));
    }

    public function testResolveDefineAttributeWithTypeExtended(): void
    {
        $typeExtended = $this->createMock(TypeExtended::class);
        $typeExtended->method('rule')->willReturn('string');

        $defineAttribute = new Define($typeExtended);
        $parameter = $this->createParameterWithDefineAttribute($defineAttribute);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('string'));
    }


    private function createParameterWithDefineAttribute(Define $defineAttribute): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);
        $reflectionAttribute = $this->createMock(ReflectionAttribute::class);

        $reflectionAttribute->method('newInstance')->willReturn($defineAttribute);

        $parameter->method('getAttributes')
            ->with(Define::class)
            ->willReturn([$reflectionAttribute]);

        return $parameter;
    }

    private function createParameterWithoutDefineAttribute(): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);

        $parameter->method('getAttributes')
            ->with(Define::class)
            ->willReturn([]);

        return $parameter;
    }
}
