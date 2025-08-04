<?php

declare(strict_types=1);

namespace Constructo\Test\Factory;

use Constructo\Contract\Reflect\SpecsFactory;
use Constructo\Contract\Reflect\TypesFactory;
use Constructo\Core\Reflect\Introspection\Introspector;
use Constructo\Factory\ReflectorFactory;
use Constructo\Factory\SchemaFactory;
use Constructo\Support\Cache;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use Constructo\Support\Metadata\Schema\Registry\Types;
use Constructo\Support\Reflective\Notation;
use PHPUnit\Framework\TestCase;

final class ReflectorFactoryTest extends TestCase
{
    public function testMakeCreatesReflectorWithAllDependencies(): void
    {
        $types = new Types();
        $cache = new Cache();
        $introspector = new Introspector();
        $notation = Notation::SNAKE;

        $typesFactory = $this->createMock(TypesFactory::class);
        $typesFactory->method('make')
            ->willReturn($types);

        $specs = new Specs();
        $specsFactory = $this->createMock(SpecsFactory::class);
        $specsFactory->method('make')
            ->willReturn($specs);
        $schemaFactory = new SchemaFactory($specsFactory);

        $factory = new ReflectorFactory($typesFactory, $schemaFactory, $cache, $introspector, $notation);

        $reflector = $factory->make();

        // Test that the reflector can be used for reflection
        $this->assertNotNull($reflector);
    }

    public function testMakeCreatesReflectorWithDefaultNotation(): void
    {
        $types = new Types();
        $cache = new Cache();
        $introspector = new Introspector();

        $typesFactory = $this->createMock(TypesFactory::class);
        $typesFactory->method('make')
            ->willReturn($types);

        $specs = new Specs();
        $specsFactory = $this->createMock(SpecsFactory::class);
        $specsFactory->method('make')
            ->willReturn($specs);
        $schemaFactory = new SchemaFactory($specsFactory);

        $factory = new ReflectorFactory($typesFactory, $schemaFactory, $cache, $introspector);

        $reflector = $factory->make();

        // Test that the reflector is created successfully with default notation
        $this->assertNotNull($reflector);
    }

    public function testMakeCallsTypesFactoryMake(): void
    {
        $types = new Types();
        $cache = new Cache();
        $introspector = new Introspector();

        $typesFactory = $this->createMock(TypesFactory::class);
        $typesFactory->expects($this->once())
            ->method('make')
            ->willReturn($types);

        $specs = new Specs();
        $specsFactory = $this->createMock(SpecsFactory::class);
        $specsFactory->method('make')
            ->willReturn($specs);
        $schemaFactory = new SchemaFactory($specsFactory);

        $factory = new ReflectorFactory($typesFactory, $schemaFactory, $cache, $introspector);

        $factory->make();
    }

    public function testMakeWithDifferentNotations(): void
    {
        $types = new Types();
        $cache = new Cache();
        $introspector = new Introspector();

        $typesFactory = $this->createMock(TypesFactory::class);
        $typesFactory->method('make')
            ->willReturn($types);

        $specs = new Specs();
        $specsFactory = $this->createMock(SpecsFactory::class);
        $specsFactory->method('make')
            ->willReturn($specs);
        $schemaFactory = new SchemaFactory($specsFactory);

        // Test with CAMEL notation
        $factoryCamel = new ReflectorFactory($typesFactory, $schemaFactory, $cache, $introspector, Notation::CAMEL);
        $reflectorCamel = $factoryCamel->make();
        $this->assertNotNull($reflectorCamel);

        // Test with PASCAL notation
        $factoryPascal = new ReflectorFactory($typesFactory, $schemaFactory, $cache, $introspector, Notation::PASCAL);
        $reflectorPascal = $factoryPascal->make();
        $this->assertNotNull($reflectorPascal);

        // Test with KEBAB notation
        $factoryKebab = new ReflectorFactory($typesFactory, $schemaFactory, $cache, $introspector, Notation::KEBAB);
        $reflectorKebab = $factoryKebab->make();
        $this->assertNotNull($reflectorKebab);
    }
}
