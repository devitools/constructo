<?php

declare(strict_types=1);

namespace Constructo\Test\Testing;

use Constructo\Core\Serialize\Builder;
use Constructo\Testing\BuilderExtension;
use Constructo\Testing\MakeExtension;
use PHPUnit\Framework\TestCase;

final class BuilderExtensionTest extends TestCase
{
    public function testShouldProvideBuilderInstance(): void
    {
        $testClass = new BuilderExtensionTestWrapper();

        $builder = $testClass->getBuilder();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    public function testShouldReturnSameBuilderInstanceOnMultipleCalls(): void
    {
        $testClass = new BuilderExtensionTestWrapper();

        $builder1 = $testClass->getBuilder();
        $builder2 = $testClass->getBuilder();

        $this->assertSame($builder1, $builder2);
    }
}

final class BuilderExtensionTestWrapper
{
    use BuilderExtension;
    use MakeExtension;

    public function getBuilder(): Builder
    {
        return $this->builder();
    }
}
