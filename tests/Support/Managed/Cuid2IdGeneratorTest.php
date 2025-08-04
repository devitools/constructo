<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Managed;

use Constructo\Exception\ManagedException;
use Constructo\Support\Managed\Cuid2IdGenerator;
use PHPUnit\Framework\TestCase;

final class Cuid2IdGeneratorTest extends TestCase
{
    public function testGenerateReturnsValidId(): void
    {
        $generator = new Cuid2IdGenerator();

        $id = $generator->generate();

        $this->assertIsString($id);
        $this->assertNotEmpty($id);
        $this->assertEquals(10, strlen($id));
    }

    public function testGenerateWithCustomLength(): void
    {
        $generator = new Cuid2IdGenerator(15);

        $id = $generator->generate();

        $this->assertIsString($id);
        $this->assertEquals(15, strlen($id));
    }

    public function testGenerateThrowsManagedExceptionForInvalidLength(): void
    {
        $this->expectException(ManagedException::class);
        $this->expectExceptionMessage('Error generating "id"');

        $generator = new Cuid2IdGenerator(2);
        $generator->generate();
    }

    public function testGenerateCreatesUniqueIds(): void
    {
        $generator = new Cuid2IdGenerator();

        $id1 = $generator->generate();
        $id2 = $generator->generate();

        $this->assertNotEquals($id1, $id2);
    }

    public function testGenerateWithMaximumValidLength(): void
    {
        $generator = new Cuid2IdGenerator(32);

        $id = $generator->generate();

        $this->assertIsString($id);
        $this->assertEquals(32, strlen($id));
    }

    public function testGenerateThrowsManagedExceptionForTooLargeLength(): void
    {
        $this->expectException(ManagedException::class);
        $this->expectExceptionMessage('Error generating "id"');

        $generator = new Cuid2IdGenerator(50);
        $generator->generate();
    }
}
