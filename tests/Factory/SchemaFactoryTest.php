<?php

declare(strict_types=1);

namespace Constructo\Test\Factory;

use Constructo\Contract\Reflect\SpecsFactory;
use Constructo\Factory\SchemaFactory;
use Constructo\Support\Metadata\Schema\Registry\Specs;
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

        // Test that schema can add fields and work with the specs
        $field = $schema->add('test_field');
        $this->assertNotNull($field);
    }

    public function testMakeCreatesSchemaWithEmptySpecs(): void
    {
        $specs = new Specs();

        $specsFactory = $this->createMock(SpecsFactory::class);
        $specsFactory->method('make')->willReturn($specs);

        $factory = new SchemaFactory($specsFactory);

        $schema = $factory->make();

        // Test that schema works even with empty specs
        $field = $schema->add('test_field');
        $this->assertNotNull($field);
        $this->assertSame(['test_field' => []], $schema->rules());
    }
}
