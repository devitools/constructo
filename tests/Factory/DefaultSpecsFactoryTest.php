<?php

declare(strict_types=1);

namespace Constructo\Test\Factory;

use Constructo\Core\Serialize\Builder;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Testing\MakeExtension;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DefaultSpecsFactoryTest extends TestCase
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

    public function testMakeReturnsRegistryWithDefaultBehavior(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $registry = $factory->make();

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

        // Test that all specs are registered correctly
        $this->assertTrue($registry->has('required'));
        $this->assertTrue($registry->has('string'));
        $this->assertTrue($registry->has('min'));

        // Test that spec objects can be retrieved
        $this->assertNotNull($registry->get('required'));
        $this->assertNotNull($registry->get('min'));
    }

    public function testValidateAcceptsValidStringNameAndArrayProperties(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $this->expectNotToPerformAssertions();
        $factory->validate('required', []);
    }

    public function testValidateAcceptsValidStringNameAndArrayPropertiesWithData(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $this->expectNotToPerformAssertions();
        $factory->validate('min', ['params' => ['min']]);
    }

    public function testValidateThrowsExceptionForNonStringName(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec name must be a string, integer given.');

        $factory->validate(123, []);
    }

    public function testValidateThrowsExceptionForNullName(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec name must be a string, NULL given.');

        $factory->validate(null, []);
    }

    public function testValidateThrowsExceptionForBooleanName(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec name must be a string, boolean given.');

        $factory->validate(true, []);
    }

    public function testValidateThrowsExceptionForArrayName(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec name must be a string, array given.');

        $factory->validate([], []);
    }

    public function testValidateThrowsExceptionForNonArrayProperties(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec properties must be an array, string given.');

        $factory->validate('required', 'invalid');
    }

    public function testValidateThrowsExceptionForNullProperties(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec properties must be an array, NULL given.');

        $factory->validate('required', null);
    }

    public function testValidateThrowsExceptionForIntegerProperties(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec properties must be an array, integer given.');

        $factory->validate('required', 123);
    }

    public function testValidateThrowsExceptionForBooleanProperties(): void
    {
        $builder = $this->make(Builder::class);
        $factory = new DefaultSpecsFactory($builder);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec properties must be an array, boolean given.');

        $factory->validate('required', false);
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
        $this->assertTrue($registry->has('string'));
        $this->assertTrue($registry->has('integer'));
        $this->assertTrue($registry->has('min'));
        $this->assertTrue($registry->has('max'));
        $this->assertTrue($registry->has('between'));
        $this->assertTrue($registry->has('in'));
        $this->assertTrue($registry->has('regex'));
        $this->assertNotNull($registry->get('min'));
        $this->assertNotNull($registry->get('max'));
    }
}
