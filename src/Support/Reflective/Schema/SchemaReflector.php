<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Schema;

use Constructo\Core\Metadata\Schema;
use Constructo\Core\Metadata\Schema\Field;
use Constructo\Support\Cache;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Support\Reflective\Notation;
use Constructo\Support\Reflective\Schema\Parameter\ManagedChain;
use Constructo\Support\Reflective\Schema\Parameter\Registry\Types;
use Constructo\Support\Reflective\Schema\Parameter\RequirementChain;
use Constructo\Support\Reflective\Schema\Parameter\TypeChain;
use ReflectionException;
use ReflectionParameter;

use function array_pop;
use function assert;
use function Constructo\Notation\format;
use function implode;
use function in_array;
use function sprintf;

class SchemaReflector
{
    private array $currentPath = [];

    public function __construct(
        protected readonly Cache $cache,
        protected readonly ?Types $types = null,
        protected readonly Notation $notation = Notation::SNAKE,
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function extract(string $source, Schema $schema): Schema
    {
        $this->cache->reset();
        $this->currentPath = [];

        $parameters = $this->getParametersFromCache($source);
        $this->extractFields($parameters, $schema);

        return $schema;
    }

    /**
     * @throws ReflectionException
     */
    protected function extractFields(array $parameters, Schema $schema, ?Field $parent = null, array $path = []): void
    {
        $chain = (new TypeChain($this->types))
            ->then(new RequirementChain($parent))
            ->then(new ManagedChain());

        foreach ($parameters as $parameter) {
            assert($parameter instanceof ReflectionParameter);
            $nestedPath = [
                ...$path,
                format($parameter->getName(), $this->notation),
            ];
            $name = implode('.', $nestedPath);

            $field = $schema->add($name);
            $chain->resolve($parameter, $field, $nestedPath);

            $source = $field->getSource();
            if ($source === null) {
                continue;
            }

            if ($this->wouldCauseCircularReference($source)) {
                continue;
            }

            $this->extractFieldsNestedSource($source, $schema, $field, $nestedPath);
        }
    }

    /**
     * @throws ReflectionException
     */
    private function extractFieldsNestedSource(string $source, Schema $schema, Field $parent, array $path): void
    {
        $nestedParameters = $this->getParametersFromCache($source);
        if (empty($nestedParameters)) {
            return;
        }

        $this->currentPath[] = $source;
        $this->extractFields($nestedParameters, $schema, $parent, $path);
        array_pop($this->currentPath);
    }

    private function wouldCauseCircularReference(string $source): bool
    {
        return in_array($source, $this->currentPath, true);
    }

    private function getParametersFromCache(string $source): array
    {
        $cacheKey = sprintf("parameters:%s", $source);

        $parameters = $this->cache->get($cacheKey);
        if ($parameters === null) {
            $parameters = $this->extractParameters($source);
            $this->cache->set($cacheKey, $parameters);
        }

        return $parameters;
    }

    protected function extractParameters(string $source): array
    {
        try {
            return Target::createFrom($source)
                ->getReflectionParameters();
        } catch (ReflectionException) {
            return [];
        }
    }
}
