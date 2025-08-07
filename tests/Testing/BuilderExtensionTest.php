<?php

declare(strict_types=1);

namespace Constructo\Test\Testing;

use Constructo\Testing\Stub\BuilderExtensionWrapper;
use PHPUnit\Framework\TestCase;

final class BuilderExtensionTest extends TestCase
{
    public function testShouldReturnSameBuilderInstanceOnMultipleCalls(): void
    {
        $testClass = new BuilderExtensionWrapper();

        $builder1 = $testClass->getBuilder();
        $builder2 = $testClass->getBuilder();

        $this->assertSame($builder1, $builder2);
    }
}
