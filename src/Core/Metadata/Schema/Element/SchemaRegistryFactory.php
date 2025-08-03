<?php

declare(strict_types=1);

namespace Constructo\Core\Metadata\Schema\Element;

use InvalidArgumentException;

use function assert;
use function Constructo\Cast\arrayify;
use function gettype;
use function Constructo\Cast\stringify;

readonly class SchemaRegistryFactory
{
    public function __construct(
        private array $types = [],
        private array $specs = [],
    ) {
    }

    public function make(): SchemaRegistry
    {
        $registry = new SchemaRegistry($this->types);
        assert($registry instanceof SchemaRegistry);
        foreach ($this->specs as $key => $value) {
            $this->validate($key, $value);
            $registry->register(stringify($key), arrayify($value));
        }
        return $registry;
    }

    public function validate(mixed $name, mixed $properties): void
    {
        if (! is_string($name)) {
            $given = gettype($name);
            throw new InvalidArgumentException(sprintf('Spec name must be a string, %s given.', $given));
        }
        if (! is_array($properties)) {
            $given = gettype($properties);
            throw new InvalidArgumentException(sprintf('Spec properties must be an array, %s given.', $given));
        }
    }
}
