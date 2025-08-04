<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Schema\Parameter\Registry;

use Constructo\Core\Metadata\Schema;
use Constructo\Factory\SchemaFactory;
use Constructo\Support\Cache;
use Constructo\Support\Reflective\Schema\SchemaReflector;
use ReflectionException;

class TypesFactory extends SchemaFactory
{
    /**
     * @template T of object
     * @param class-string<T>|null $source
     * @throws ReflectionException
     */
    public function makeFrom(string $source = null): Schema
    {
        $types = (new TypesFactory())->make();
        $schema = $this->make();
        return (new SchemaReflector(new Cache(), $types))->extract($source, $schema);
    }
}
