<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Reflect\Resolve;

use Constructo\Core\Reflect\Resolve\RequirementChain;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

final class RequirementChainTest extends TestCase
{
    private Specs $specs;

    protected function setUp(): void
    {
        $specsData = [
            'required' => [],
            'nullable' => [],
            'sometimes' => [],
            'present' => [],
            'filled' => [],
        ];

        $specsFactory = new DefaultSpecsFactory($specsData);
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
        $chain = new RequirementChain();

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
        $chain = new RequirementChain();

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
        $chain = new RequirementChain();

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
        $chain = new RequirementChain();

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
        $chain = new RequirementChain($parentField);

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
        $chain = new RequirementChain();

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
        $chain = new RequirementChain();

        $chain->resolve($parameter, $field, ['test']);

        $this->assertTrue($field->hasRule('nullable'));
        $this->assertFalse($field->hasRule('filled'));
    }

    public function testConstructorWithParentAndSpecs(): void
    {
        $parentField = new Field($this->specs, new Rules(), 'parent');
        $specs = new Specs();

        $chain = new RequirementChain($parentField, $specs);

        $this->assertInstanceOf(RequirementChain::class, $chain);
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
