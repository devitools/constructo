<?php

declare(strict_types=1);

namespace Constructo\Test\Testing;

use Constructo\Support\Managed;
use Constructo\Testing\MakeExtension;
use Constructo\Testing\ManagedExtension;
use PHPUnit\Framework\TestCase;

final class ManagedExtensionTest extends TestCase
{
    public function testShouldProvideManagedInstance(): void
    {
        $testClass = new ManagedExtensionTestWrapper();

        $managed = $testClass->getManaged();

        $this->assertInstanceOf(Managed::class, $managed);
    }

    public function testShouldReturnSameManagedInstanceOnMultipleCalls(): void
    {
        $testClass = new ManagedExtensionTestWrapper();

        $managed1 = $testClass->getManaged();
        $managed2 = $testClass->getManaged();

        $this->assertSame($managed1, $managed2);
    }
}

final class ManagedExtensionTestWrapper
{
    use ManagedExtension;
    use MakeExtension;

    public function getManaged(): Managed
    {
        return $this->managed();
    }
}
