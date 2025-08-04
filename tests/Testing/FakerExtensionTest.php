<?php

declare(strict_types=1);

namespace Constructo\Test\Testing;

use Constructo\Core\Fake\Faker;
use Constructo\Testing\FakerExtension;
use Constructo\Testing\MakeExtension;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class FakerExtensionTest extends TestCase
{
    public function testShouldProvideFakerInstance(): void
    {
        $testClass = new FakerExtensionTestWrapper();

        $faker = $testClass->getFaker();

        $this->assertInstanceOf(Faker::class, $faker);
    }

    public function testShouldReturnSameFakerInstanceOnMultipleCalls(): void
    {
        $testClass = new FakerExtensionTestWrapper();

        $faker1 = $testClass->getFaker();
        $faker2 = $testClass->getFaker();

        $this->assertSame($faker1, $faker2);
    }

    public function testShouldProvideGeneratorInstance(): void
    {
        $testClass = new FakerExtensionTestWrapper();

        $generator = $testClass->getGenerator();

        $this->assertInstanceOf(Generator::class, $generator);
    }

    public function testShouldReturnSameGeneratorInstanceOnMultipleCalls(): void
    {
        $testClass = new FakerExtensionTestWrapper();

        $generator1 = $testClass->getGenerator();
        $generator2 = $testClass->getGenerator();

        $this->assertSame($generator1, $generator2);
    }
}

final class FakerExtensionTestWrapper
{
    use FakerExtension;
    use MakeExtension;

    public function getFaker(): Faker
    {
        return $this->faker();
    }

    public function getGenerator(): Generator
    {
        return $this->generator();
    }
}
