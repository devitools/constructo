<?php

declare(strict_types=1);

namespace Constructo\Factory;

use Constructo\Contract\Reflect\TypesFactory;
use Constructo\Core\Reflect\Reflector;
use Constructo\Support\Cache;
use Constructo\Support\Reflective\Notation;

readonly class ReflectorFactory
{
    public function __construct(
        private TypesFactory $typesFactory,
        private SchemaFactory $schemaFactory,
        private Cache $cache,
        private Notation $notation = Notation::SNAKE,
    ) {
    }

    public function make(): Reflector
    {
        $types = $this->typesFactory->make();
        return new Reflector($this->schemaFactory, $types, $this->cache, $this->notation);
    }
}
