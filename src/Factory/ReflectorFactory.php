<?php

declare(strict_types=1);

namespace Constructo\Factory;

use Constructo\Contract\Reflect\TypesFactory;
use Constructo\Core\Reflect\Introspection\Introspector;
use Constructo\Core\Reflect\Reflector;
use Constructo\Core\Serialize\Builder;
use Constructo\Support\Cache;
use Constructo\Support\Reflective\Notation;
use Constructo\Testing\MakeExtension;

use function Constructo\Cast\arrayify;

use const CONSTRUCTO_SCHEMATA;

readonly class ReflectorFactory
{
    use MakeExtension;

    public function __construct(
        private TypesFactory $typesFactory,
        private SchemaFactory $schemaFactory,
        private Cache $cache,
        private Introspector $introspector,
        private Notation $notation = Notation::SNAKE,
    ) {
    }

    public static function createFrom(array $specs = [], array $types = [], Notation $notation = Notation::SNAKE): self
    {
        $specs = array_merge(self::extract('specs'), $specs);
        $types = array_merge(self::extract('types'), $types);
        $builder = new Builder($notation);
        $specsFactory = new DefaultSpecsFactory($builder, $specs);
        return new self(
            new DefaultTypesFactory($types),
            new SchemaFactory($specsFactory),
            new Cache(),
            new Introspector(),
            $notation
        );
    }

    public function make(): Reflector
    {
        $types = $this->typesFactory->make();
        return new Reflector($this->schemaFactory, $types, $this->cache, $this->introspector, $this->notation);
    }

    private static function extract(string $key): array
    {
        $schemata = CONSTRUCTO_SCHEMATA;
        $extracted = $schemata[$key] ?? null;
        return is_array($extracted) ? $extracted : [];
    }
}
