<?php

declare(strict_types=1);

namespace Constructo\Core\Metadata\Schema;

use Constructo\Core\Metadata\Schema;
use Constructo\Core\Metadata\Schema\Field\Fieldset;
use Constructo\Core\Metadata\Schema\Registry\RegistryFactory;
use Constructo\Support\Cache;
use Constructo\Support\Reflective\Schema\SchemaReflector;
use ReflectionException;

class SchemaFactory
{
    public function make(): Schema
    {
        $schemaRegistry = (new RegistryFactory())->make();
        return new Schema($schemaRegistry, new Fieldset());
    }

    /**
     * @template T of object
     * @param class-string<T>|null $source
     * @throws ReflectionException
     */
    public function makeFrom(string $source = null): Schema
    {
        $schemaRegistry = (new RegistryFactory())->make();
        $schema = new Schema($schemaRegistry, new Fieldset());
        return (new SchemaReflector(new Cache(), $schemaRegistry))->extract($source, $schema);
    }
}
