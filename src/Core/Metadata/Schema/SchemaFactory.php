<?php

declare(strict_types=1);

namespace Constructo\Core\Metadata\Schema;

use Constructo\Core\Metadata\Schema;
use Constructo\Core\Metadata\Schema\Element\SchemaRegistryFactory;
use Constructo\Support\Cache;
use Constructo\Support\Reflective\SchemaReflector;
use ReflectionException;

class SchemaFactory
{
    public function make(): Schema
    {
        $schemaRegistry = (new SchemaRegistryFactory())->make();
        return new Schema($schemaRegistry, new Fieldset());
    }

    /**
     * @template T of object
     * @param class-string<T>|null $source
     * @throws ReflectionException
     */
    public function makeFrom(string $source = null): Schema
    {
        $schemaRegistry = (new SchemaRegistryFactory())->make();
        $schema = new Schema($schemaRegistry, new Fieldset());
        return (new SchemaReflector(new Cache(), $schemaRegistry))->extract($source, $schema);
    }
}
