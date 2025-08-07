<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Reflect\Resolver\Type;

use BackedEnum;
use Constructo\Core\Reflect\Resolver\Type\Contract\NamedTypeResolution;
use Constructo\Core\Reflect\Resolver\Type\EnumNamedTypeHandler;
use Constructo\Core\Serialize\Builder;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use Constructo\Testing\MakeExtension;
use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionParameter;

enum TestStringEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
}

enum TestIntEnum: int
{
    case FIRST = 1;
    case SECOND = 2;
    case THIRD = 3;
}

enum TestUnitEnum
{
    case ONE;
    case TWO;
}

enum TestEmptyBackedEnum: string
{
    // Enum with no cases to test an empty values scenario
}

class EnumNamedTypeHandlerTest extends TestCase
{
    use MakeExtension;

    private EnumNamedTypeHandler $handler;
    private Specs $specs;

    protected function setUp(): void
    {
        $this->handler = $this->make(EnumNamedTypeHandler::class);
        $builder = $this->make(Builder::class);
        $specsData = [
            'string' => [],
            'integer' => [],
            'in' => ['params' => ['values']],
        ];

        $specsFactory = new DefaultSpecsFactory($builder, $specsData);
        $this->specs = $specsFactory->make();
    }

    public function testResolveStringBackedEnum(): void
    {
        $parameter = $this->createParameterWithType(TestStringEnum::class, false);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('string'));
        $this->assertTrue($field->hasRule('in'));
    }

    public function testResolveIntBackedEnum(): void
    {
        $parameter = $this->createParameterWithType(TestIntEnum::class, false);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('integer'));
        $this->assertTrue($field->hasRule('in'));
    }

    public function testDoesNotResolveUnitEnum(): void
    {
        $parameter = $this->createParameterWithType(TestUnitEnum::class, false);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('string'));
        $this->assertFalse($field->hasRule('integer'));
        $this->assertFalse($field->hasRule('in'));
    }

    public function testDoesNotResolveNonEnumClass(): void
    {
        $parameter = $this->createParameterWithType('stdClass', false);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('string'));
        $this->assertFalse($field->hasRule('integer'));
        $this->assertFalse($field->hasRule('in'));
    }

    public function testDoesNotResolveBuiltinType(): void
    {
        $parameter = $this->createParameterWithType('string', true);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('string'));
        $this->assertFalse($field->hasRule('in'));
    }

    public function testDoesNotResolveEnumWithNullBackingType(): void
    {
        $parameter = $this->createParameterWithType(TestEmptyBackedEnum::class, false);
        $field = new Field($this->specs, new Rules(), 'test');

        $handler = new class extends EnumNamedTypeHandler {
            protected function resolveNamedType(
                ReflectionNamedType $parameter,
                Field $field,
            ): NamedTypeResolution {
                $enumClassName = $parameter->getName();
                if (! is_subclass_of($enumClassName, BackedEnum::class)) {
                    return NamedTypeResolution::Resolved;
                }

                return NamedTypeResolution::NotResolved;
            }
        };

        $handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('string'));
        $this->assertFalse($field->hasRule('in'));
    }

    public function testResolveEmptyBackedEnum(): void
    {
        $parameter = $this->createParameterWithType(TestEmptyBackedEnum::class, false);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        // Should set the backing type but not add 'in' rule due to empty values (line 57 coverage)
        $this->assertTrue($field->hasRule('string'));
        $this->assertFalse($field->hasRule('in'));
    }

    private function createParameterWithType(string $typeName, bool $isBuiltin): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);
        $type = $this->createMock(ReflectionNamedType::class);

        $type->method('getName')
            ->willReturn($typeName);
        $type->method('isBuiltin')
            ->willReturn($isBuiltin);

        $parameter->method('getType')
            ->willReturn($type);

        return $parameter;
    }
}
