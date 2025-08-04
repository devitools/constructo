<?php

declare(strict_types=1);

namespace Constructo\Test\Factory;

use Constructo\Factory\DefaultTypesFactory;
use Constructo\Support\Metadata\Schema\Registry\Types;
use PHPUnit\Framework\TestCase;

final class DefaultTypesFactoryTest extends TestCase
{
    public function testMakeCreatesTypesWithProvidedTypes(): void
    {
        $customTypes = [
            'CustomClass' => 'custom_type',
            'AnotherClass' => 'another_type',
        ];

        $factory = new DefaultTypesFactory($customTypes);

        $types = $factory->make();

        $this->assertInstanceOf(Types::class, $types);
        $this->assertTrue($types->has('CustomClass'));
        $this->assertTrue($types->has('AnotherClass'));
        $this->assertSame('custom_type', $types->get('CustomClass'));
        $this->assertSame('another_type', $types->get('AnotherClass'));
    }

    public function testMakeCreatesTypesWithEmptyArray(): void
    {
        $factory = new DefaultTypesFactory([]);

        $types = $factory->make();

        $this->assertInstanceOf(Types::class, $types);
        // Should still have default types
        $this->assertTrue($types->has('DateTime'));
        $this->assertSame('date', $types->get('DateTime'));
    }

    public function testMakeCreatesTypesWithDefaultsAndCustomTypes(): void
    {
        $customTypes = [
            'MyClass' => 'my_type',
            'DateTime' => 'custom_date', // Override default
        ];

        $factory = new DefaultTypesFactory($customTypes);

        $types = $factory->make();

        // Custom type should be available
        $this->assertTrue($types->has('MyClass'));
        $this->assertSame('my_type', $types->get('MyClass'));

        // Custom override should work
        $this->assertTrue($types->has('DateTime'));
        $this->assertSame('custom_date', $types->get('DateTime'));

        // Other defaults should still be available
        $this->assertTrue($types->has('DateTimeImmutable'));
        $this->assertSame('date', $types->get('DateTimeImmutable'));
    }

    public function testMakeCreatesNewInstanceEachTime(): void
    {
        $factory = new DefaultTypesFactory(['TestClass' => 'test_type']);

        $types1 = $factory->make();
        $types2 = $factory->make();

        $this->assertNotSame($types1, $types2);
        $this->assertEquals($types1->get('TestClass'), $types2->get('TestClass'));
    }
}
