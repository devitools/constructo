<?php

declare(strict_types=1);

namespace Constructo\Factory;

use Constructo\Contract\Reflect\SpecsFactory;
use Constructo\Core\Serialize\Builder;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use InvalidArgumentException;

use function Constructo\Cast\arrayify;
use function Constructo\Cast\stringify;
use function gettype;
use function is_array;
use function is_string;
use function sprintf;

readonly class DefaultSpecsFactory implements SpecsFactory
{
    public function __construct(
        private Builder $builder,
        private array $specs = [],
    ) {
    }

    public function make(): Specs
    {
        $registry = new Specs($this->builder);
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
