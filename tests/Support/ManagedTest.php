<?php

declare(strict_types=1);

namespace Constructo\Test\Support;

use Constructo\Contract\Managed\IdGenerator;
use Constructo\Exception\ManagedException;
use Constructo\Support\Managed;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ManagedTest extends TestCase
{
    public function testIdGeneratesValidString(): void
    {
        $managed = new Managed();

        $id = $managed->id();

        $this->assertIsString($id);
        $this->assertNotEmpty($id);
    }

    public function testIdWithCustomLength(): void
    {
        $managed = new Managed(15);

        $id = $managed->id();

        $this->assertIsString($id);
        $this->assertEquals(15, strlen($id));
    }

    public function testIdThrowsManagedExceptionWhenGeneratorFails(): void
    {
        $mockGenerator = $this->createMock(IdGenerator::class);
        $mockGenerator->expects($this->once())
            ->method('generate')
            ->willThrowException(new ManagedException('id', new RuntimeException('Generator failed')));

        $managed = new Managed(10, $mockGenerator);

        $this->expectException(ManagedException::class);
        $this->expectExceptionMessage('Error generating "id": "Generator failed"');

        $managed->id();
    }

    public function testNowReturnsTimestampString(): void
    {
        $managed = new Managed();

        $timestamp = $managed->now();

        $this->assertIsString($timestamp);
        $this->assertNotEmpty($timestamp);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $timestamp);
    }

    public function testIdGeneratesUniqueValues(): void
    {
        $managed = new Managed();

        $id1 = $managed->id();
        $id2 = $managed->id();

        $this->assertNotEquals($id1, $id2);
    }

    public function testCustomIdGeneratorIsUsed(): void
    {
        $mockGenerator = $this->createMock(IdGenerator::class);
        $mockGenerator->expects($this->once())
            ->method('generate')
            ->willReturn('custom-id-123');

        $managed = new Managed(10, $mockGenerator);

        $id = $managed->id();

        $this->assertEquals('custom-id-123', $id);
    }
}
