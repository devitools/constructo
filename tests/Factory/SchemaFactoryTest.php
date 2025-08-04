<?php

declare(strict_types=1);

namespace Constructo\Test\Factory;

use Constructo\Contract\Schema\SpecsFactory;
use Constructo\Core\Metadata\Schema\Registry\Specs;
use Constructo\Factory\SchemaFactory;
use PHPUnit\Framework\TestCase;

final class SchemaFactoryTest extends TestCase
{
    public function testMakeCreatesSchemaWithSpecsFromFactory(): void
    {
        $specs = new Specs();
        $specs->register('required', []);

        $specsFactory = $this->createMock(SpecsFactory::class);
        $specsFactory->method('make')->willReturn($specs);

        $factory = new SchemaFactory($specsFactory);

        $schema = $factory->make();

        $this->assertTrue($schema->hasSpec('required'));
        $this->assertFalse($schema->hasSpec('nonexistent'));
    }

    public function testMakeCreatesSchemaWithEmptySpecs(): void
    {
        $specs = new Specs();

        $specsFactory = $this->createMock(SpecsFactory::class);
        $specsFactory->method('make')->willReturn($specs);

        $factory = new SchemaFactory($specsFactory);

        $schema = $factory->make();

        $this->assertFalse($schema->hasSpec('required'));
        $this->assertFalse($schema->hasSpec('nullable'));
    }
}
