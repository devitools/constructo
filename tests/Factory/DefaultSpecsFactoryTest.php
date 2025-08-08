<?php

declare(strict_types=1);

namespace Constructo\Test\Factory;

use Constructo\Core\Serialize\Builder;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Testing\MakeExtension;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DefaultSpecsFactoryTest extends TestCase
{
    use MakeExtension;

    public function testCanCreateSpecsFactoryWithEmptySpecs(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $registry = $factory->make();

        $this->assertFalse($registry->has('nonexistent'));
    }

    public function testCanCreateSpecsFactoryWithSpecs(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'required' => [],
            'nullable' => [],
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);

        $registry = $factory->make();

        $this->assertTrue($registry->has('required'));
        $this->assertTrue($registry->has('nullable'));
        $this->assertFalse($registry->has('nonexistent'));
    }

    public function testMakeRegistersSpecsCorrectly(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'required' => [],
            'string' => [],
            'min' => ['params' => ['min']],
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);
        $registry = $factory->make();

        $this->assertTrue($registry->has('required'));
        $this->assertTrue($registry->has('string'));
        $this->assertTrue($registry->has('min'));
    }

    public function testMakeWithComplexSpecs(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'required' => [],
            'string' => [],
            'integer' => [],
            'min' => ['params' => ['min']],
            'max' => ['params' => ['max']],
            'between' => [
                'params' => [
                    'min',
                    'max',
                ],
            ],
            'in' => ['params' => ['values']],
            'regex' => [
                'params' => [
                    'pattern',
                    'parameters:optional',
                ],
            ],
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);
        $registry = $factory->make();

        $this->assertTrue($registry->has('required'));
        $this->assertTrue($registry->has('between'));
        $this->assertTrue($registry->has('regex'));
    }

    // Test scenarios that trigger validation errors in the validate method

    public function testMakeThrowsExceptionWhenSpecNameIsNotString(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            123 => [], // Integer key instead of string
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec name must be a string, integer given.');

        $factory->make();
    }

    public function testMakeThrowsExceptionWhenSpecPropertiesIsNotArray(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'required' => 'invalid', // String value instead of array
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec properties must be an array, string given.');

        $factory->make();
    }

    public function testMakeThrowsExceptionWhenSpecPropertiesIsInteger(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'required' => 123, // Integer value instead of array
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec properties must be an array, integer given.');

        $factory->make();
    }

    public function testMakeThrowsExceptionWhenSpecPropertiesIsNull(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'required' => null, // Null value instead of array
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec properties must be an array, NULL given.');

        $factory->make();
    }

    public function testMakeThrowsExceptionWhenSpecPropertiesIsBoolean(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'required' => true, // Boolean value instead of array
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec properties must be an array, boolean given.');

        $factory->make();
    }

    public function testMakeThrowsExceptionWhenSpecPropertiesIsFloat(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'required' => 3.14, // Float value instead of array
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec properties must be an array, double given.');

        $factory->make();
    }

    public function testMakeThrowsExceptionWhenSpecPropertiesIsObject(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'required' => new \stdClass(), // Object value instead of array
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec properties must be an array, object given.');

        $factory->make();
    }

    public function testMakeWithMixedValidAndInvalidSpecs(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'valid_spec' => [],
            123 => [], // This will trigger the validation error
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec name must be a string, integer given.');

        $factory->make();
    }

    public function testMakeHandlesEmptyArrayPropertiesCorrectly(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'empty_spec' => [], // Empty array - should be valid
            'spec_with_data' => ['param' => 'value'],
        ];

        $factory = new DefaultSpecsFactory($builder, $specs);
        $registry = $factory->make();

        $this->assertTrue($registry->has('empty_spec'));
        $this->assertTrue($registry->has('spec_with_data'));
    }
}
