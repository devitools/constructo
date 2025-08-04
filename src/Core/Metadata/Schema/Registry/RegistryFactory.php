<?php

declare(strict_types=1);

namespace Constructo\Core\Metadata\Schema\Registry;

use Constructo\Core\Metadata\Schema\Registry;
use InvalidArgumentException;

use function assert;
use function Constructo\Cast\arrayify;
use function Constructo\Cast\stringify;
use function gettype;

readonly class RegistryFactory
{
    public function __construct(
        private array $types = [],
        private array $specs = [],
    ) {
    }

    public function make(): Registry
    {
        $registry = new Registry($this->types);
        assert($registry instanceof Registry);
        foreach ($this->specs as $key => $value) {
            $this->validate($key, $value);
            $registry->registerSpec(stringify($key), arrayify($value));
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
