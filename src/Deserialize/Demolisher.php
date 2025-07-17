<?php

declare(strict_types=1);

namespace Morph\Deserialize;

use ReflectionException;
use ReflectionParameter;
use Morph\Contract\Collectable;
use Morph\Contract\Exportable;
use Morph\Contract\Message;
use Morph\Support\Datum;
use Morph\Support\Reflective\Engine;
use Morph\Support\Reflective\Factory\Target;
use Morph\Support\Set;
use Morph\Deserialize\Resolve\AttributeChain;
use Morph\Deserialize\Resolve\CollectionChain;
use Morph\Deserialize\Resolve\DateChain;
use Morph\Deserialize\Resolve\DependencyChain;
use Morph\Deserialize\Resolve\DoNothingChain;
use Morph\Deserialize\Resolve\FormatterChain;

use function get_object_vars;
use function Serendipity\Type\Cast\arrayify;

class Demolisher extends Engine
{
    /**
     * @throws ReflectionException
     */
    public function demolish(object $instance): object
    {
        if ($instance instanceof Datum) {
            return $instance->export();
        }
        $target = Target::createFrom($instance::class);
        $parameters = $target->getReflectionParameters();
        if (empty($parameters)) {
            return (object) [];
        }
        return $this->resolveParameters($parameters, $instance);
    }

    /**
     * @throws ReflectionException
     */
    public function demolishCollection(Collectable $collection): array
    {
        $demolished = [];
        foreach ($collection->all() as $instance) {
            if ($instance instanceof Exportable) {
                $instance = $this->demolish($instance);
            }
            $demolished[] = $instance;
        }
        return $demolished;
    }

    public function extractValues(object $instance): array
    {
        if ($instance instanceof Message) {
            return arrayify($instance->content());
        }
        if ($instance instanceof Exportable) {
            return (array) $instance->export();
        }
        return get_object_vars($instance);
    }

    /**
     * @param array<ReflectionParameter> $parameters
     */
    protected function resolveParameters(array $parameters, object $instance): object
    {
        $data = $this->extractValues($instance);
        $set = Set::createFrom($data);
        $data = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            if (! $set->has($name)) {
                continue;
            }

            $resolved = (new DoNothingChain($this->notation))
                ->then(new DependencyChain($this->notation))
                ->then(new AttributeChain($this->notation))
                ->then(new CollectionChain($this->notation))
                ->then(new DateChain($this->notation))
                ->then(new FormatterChain($this->notation, $this->formatters))
                ->resolve($parameter, $set->get($name));

            $field = $this->casedField($parameter);
            $data[$field] = $resolved->content;
        }
        return (object) $data;
    }
}
