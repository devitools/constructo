<?php

declare(strict_types=1);

namespace Constructo\Test\Testing;

use Constructo\Testing\MakeExtension;
use PHPUnit\Framework\TestCase;
use stdClass;

final class MakeExtensionTest extends TestCase
{
    public function testShouldCreateInstanceWithoutArguments(): void
    {
        $testClass = new MakeExtensionTestWrapper();

        $instance = $testClass->createInstance(stdClass::class);

        $this->assertInstanceOf(stdClass::class, $instance);
    }

    public function testShouldCreateInstanceWithArguments(): void
    {
        $testClass = new MakeExtensionTestWrapper();

        $instance = $testClass->createInstance(TestClassWithConstructor::class, ['value' => 'test']);

        $this->assertInstanceOf(TestClassWithConstructor::class, $instance);
        $this->assertSame('test', $instance->getValue());
    }

    public function testShouldCreateNewInstanceOnEachCall(): void
    {
        $testClass = new MakeExtensionTestWrapper();

        $instance1 = $testClass->createInstance(stdClass::class);
        $instance2 = $testClass->createInstance(stdClass::class);

        $this->assertNotSame($instance1, $instance2);
    }
}

final class MakeExtensionTestWrapper
{
    use MakeExtension;

    public function createInstance(string $class, array $args = []): mixed
    {
        return $this->make($class, $args);
    }
}

final readonly class TestClassWithConstructor
{
    public function __construct(private string $value)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
