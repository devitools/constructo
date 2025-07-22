<?php

declare(strict_types=1);

namespace Constructo\Core\Serialize;

use Constructo\Core\Serialize\Resolver\AttributeValue;
use Constructo\Core\Serialize\Resolver\BackedEnumValue;
use Constructo\Core\Serialize\Resolver\CollectionValue;
use Constructo\Core\Serialize\Resolver\DependencyValue;
use Constructo\Core\Serialize\Resolver\FormatValue;
use Constructo\Core\Serialize\Resolver\NoValue;
use Constructo\Core\Serialize\Resolver\TypeMatched;
use Constructo\Core\Serialize\Resolver\ValidateValue;
use Constructo\Exception\AdapterException;
use Constructo\Support\Reflective\Engine;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Support\Set;
use ReflectionException;
use ReflectionParameter;
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
