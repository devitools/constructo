<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Reflect\Resolver;

use Constructo\Core\Reflect\Resolver\RequirementResolver;
use Constructo\Core\Serialize\Builder;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use Constructo\Testing\MakeExtension;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

class RequirementResolverTest extends TestCase
{
    use MakeExtension;

    private Specs $specs;

    protected function setUp(): void
    {
        $builder = $this->make(Builder::class);
        $specsData = [
            'required' => [],
            'nullable' => [],
            'sometimes' => [],
            'present' => [],
            'filled' => [],
        ];

        $specsFactory = new DefaultSpecsFactory($builder, $specsData);
        $this->specs = $specsFactory->make();
    }

    public function testResolveWithNullableParameter(): void
    {
        $parameter = $this->createParameterMock(
            allowsNull: true,
            isOptional: false,
            isDefaultValueAvailable: false
        );
        $field = new Field($this->specs, new Rules(), 'test');
        $chain = new RequirementResolver();

        $chain->resolve($parameter, $field, ['test']);

        $this->assertTrue($field->hasRule('nullable'));
        $this->assertTrue($field->hasRule('present'));
    }

    public function testResolveWithStrictlyRequiredParameter(): void
    {
        $parameter = $this->createParameterMock(
            allowsNull: false,
            isOptional: false,
            isDefaultValueAvailable: false
        );
        $field = new Field($this->specs, new Rules(), 'test');
        $chain = new RequirementResolver();

        $chain->resolve($parameter, $field, ['test']);

        $this->assertFalse($field->hasRule('nullable'));
        $this->assertTrue($field->hasRule('required'));
    }

    public function testResolveWithOptionalParameterWithDefault(): void
    {
        $parameter = $this->createParameterMock(
            allowsNull: false,
            isOptional: true,
            isDefaultValueAvailable: true
        );
        $field = new Field($this->specs, new Rules(), 'test');
        $chain = new RequirementResolver();

        $chain->resolve($parameter, $field, ['test']);

        $this->assertFalse($field->hasRule('nullable'));
        $this->assertTrue($field->hasRule('sometimes'));
        $this->assertTrue($field->hasRule('required'));
    }

    public function testResolveWithOptionalParameterWithoutDefault(): void
    {
        $parameter = $this->createParameterMock(
            allowsNull: false,
            isOptional: true,
            isDefaultValueAvailable: false
        );
        $field = new Field($this->specs, new Rules(), 'test');
        $chain = new RequirementResolver();

        $chain->resolve($parameter, $field, ['test']);

        $this->assertFalse($field->hasRule('nullable'));
        $this->assertTrue($field->hasRule('filled'));
    }

    public function testResolveWithParentSometimesRule(): void
    {
        $parentField = new Field($this->specs, new Rules(), 'parent');
        $parentField->sometimes();

        $parameter = $this->createParameterMock(
            allowsNull: false,
            isOptional: false,
            isDefaultValueAvailable: false
        );
        $field = new Field($this->specs, new Rules(), 'test');
        $chain = new RequirementResolver($parentField);

        $chain->resolve($parameter, $field, ['test']);

        $this->assertTrue($field->hasRule('sometimes'));
        $this->assertFalse($field->hasRule('required'));
    }

    public function testResolveWithOptionalDefaultAndNullable(): void
    {
        $parameter = $this->createParameterMock(
            allowsNull: true,
            isOptional: true,
            isDefaultValueAvailable: true
        );
        $field = new Field($this->specs, new Rules(), 'test');
        $chain = new RequirementResolver();

        $chain->resolve($parameter, $field, ['test']);

        $this->assertTrue($field->hasRule('nullable'));
        $this->assertTrue($field->hasRule('sometimes'));
    }

    public function testResolveWithOptionalNullableWithoutDefault(): void
    {
        $parameter = $this->createParameterMock(
            allowsNull: true,
            isOptional: true,
            isDefaultValueAvailable: false
        );
        $field = new Field($this->specs, new Rules(), 'test');
        $chain = new RequirementResolver();

        $chain->resolve($parameter, $field, ['test']);

        $this->assertTrue($field->hasRule('nullable'));
        $this->assertFalse($field->hasRule('filled'));
    }

    private function createParameterMock(
        bool $allowsNull,
        bool $isOptional,
        bool $isDefaultValueAvailable
    ): ReflectionParameter {
        $parameter = $this->createMock(ReflectionParameter::class);

        $parameter->method('allowsNull')->willReturn($allowsNull);
        $parameter->method('isOptional')->willReturn($isOptional);
        $parameter->method('isDefaultValueAvailable')->willReturn($isDefaultValueAvailable);

        return $parameter;
    }
}
