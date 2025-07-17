<?php

declare(strict_types=1);

namespace Morph\Serialize;

use ReflectionException;
use ReflectionParameter;
use Morph\Exception\AdapterException;
use Morph\Support\Reflective\Engine;
use Morph\Support\Reflective\Factory\Target;
use Morph\Support\Set;
use Morph\Serialize\Resolver\AttributeValue;
use Morph\Serialize\Resolver\BackedEnumValue;
use Morph\Serialize\Resolver\CollectionValue;
use Morph\Serialize\Resolver\DependencyValue;
use Morph\Serialize\Resolver\FormatValue;
use Morph\Serialize\Resolver\NoValue;
use Morph\Serialize\Resolver\TypeMatched;
use Morph\Serialize\Resolver\ValidateValue;
use Throwable;

class Builder extends Engine
{
    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string> $path
     *
     * @return T
     * @throws AdapterException
     */
    public function build(string $class, Set $set, array $path = []): mixed
    {
        try {
            return $this->make($class, $set, $path);
        } catch (AdapterException $error) {
            throw $error;
        } catch (Throwable $error) {
            throw new AdapterException(values: $set, error: $error);
        }
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string> $path
     *
     * @return T
     * @throws ReflectionException
     * @throws AdapterException
     */
    protected function make(string $class, Set $set, array $path = []): mixed
    {
        $target = Target::createFrom($class);
        $parameters = $target->getReflectionParameters();
        if (empty($parameters)) {
            /* @phpstan-ignore return.type */
            return $target->getReflectionClass()->newInstance();
        }

        $resolution = new Resolution();

        $this->resolveParameters($resolution, $parameters, $set, $path);

        if (empty($resolution->errors())) {
            /* @phpstan-ignore return.type */
            return $target->getReflectionClass()->newInstanceArgs($resolution->args());
        }
        throw new AdapterException($set, $resolution->errors());
    }

    /**
     * @param array<ReflectionParameter> $parameters
     * @param array<string> $path
     */
    private function resolveParameters(Resolution $resolution, array $parameters, Set $set, array $path): void
    {
        foreach ($parameters as $parameter) {
            $nestedPath = [...$path, $parameter->getName()];
            $resolved = (new ValidateValue(notation: $this->notation, path: $nestedPath))
                ->then(new DependencyValue(notation: $this->notation, path: $nestedPath))
                ->then(new BackedEnumValue(notation: $this->notation, path: $nestedPath))
                ->then(new TypeMatched(notation: $this->notation, path: $nestedPath))
                ->then(new AttributeValue(notation: $this->notation, path: $nestedPath))
                ->then(new CollectionValue(notation: $this->notation, path: $nestedPath))
                ->then(new TypeMatched(notation: $this->notation, path: $nestedPath))
                ->then(new FormatValue($this->notation, $this->formatters, $nestedPath))
                ->then(new TypeMatched(notation: $this->notation, path: $nestedPath))
                ->then(new NoValue(notation: $this->notation, path: $nestedPath))
                ->resolve($parameter, $set);

            $resolution->add($resolved);
        }
    }
}
