<?php

declare(strict_types=1);

namespace Constructo\Core\Deserialize;

use Constructo\Contract\Collectable;
use Constructo\Contract\Exportable;
use Constructo\Contract\Message;
use Constructo\Core\Deserialize\Resolve\AttributeChain;
use Constructo\Core\Deserialize\Resolve\CollectionChain;
use Constructo\Core\Deserialize\Resolve\DateChain;
use Constructo\Core\Deserialize\Resolve\DependencyChain;
use Constructo\Core\Deserialize\Resolve\DoNothingChain;
use Constructo\Core\Deserialize\Resolve\FormatterChain;
use Constructo\Support\Datum;
use Constructo\Support\Reflective\Engine;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Support\Set;
use ReflectionException;
use ReflectionParameter;

use function Constructo\Cast\arrayify;
use function get_object_vars;

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
