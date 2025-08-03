<?php

declare(strict_types=1);

namespace Constructo\Testing\Faker;

use Constructo\Contract\Formatter;
use Constructo\Contract\Testing\Faker as Contract;
use Constructo\Support\Reflective\Engine;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Support\Reflective\Notation;
use Constructo\Support\Set;
use Constructo\Testing\Faker\Resolver\FromCollection;
use Constructo\Testing\Faker\Resolver\FromDefaultValue;
use Constructo\Testing\Faker\Resolver\FromDependency;
use Constructo\Testing\Faker\Resolver\FromEnum;
use Constructo\Testing\Faker\Resolver\FromPreset;
use Constructo\Testing\Faker\Resolver\FromTypeAttributes;
use Constructo\Testing\Faker\Resolver\FromTypeBuiltin;
use Constructo\Testing\Faker\Resolver\FromTypeDate;
use Faker\Factory;
use Faker\Generator;
use ReflectionException;
use ReflectionParameter;

use function Constructo\Cast\stringify;
use function getenv;


class Faker extends Engine implements Contract
{
    protected readonly Generator $generator;

    /**
     * @param array<callable|Formatter> $formatters
     * @SuppressWarnings(StaticAccess)
     */
    public function __construct(
        Notation $case = Notation::SNAKE,
        array $formatters = [],
        ?string $locale = null,
    ) {
        parent::__construct($case, $formatters);

        $this->generator = Factory::create($this->locale($locale));
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->generate($name, $arguments);
    }

    /**
     * @template U of object
     * @param class-string<U> $class
     * @throws ReflectionException
     */
    public function fake(string $class, array $presets = []): Set
    {
        $target = Target::createFrom($class);
        $parameters = $target->getReflectionParameters();
        if (empty($parameters)) {
            return Set::createFrom([]);
        }

        return $this->resolveParameters($parameters, new Set($presets));
    }

    public function generate(string $name, array $arguments = []): mixed
    {
        return $this->generator->__call($name, $arguments);
    }

    public function generator(): Generator
    {
        return $this->generator;
    }

    /**
     * @param array<ReflectionParameter> $parameters
     */
    private function resolveParameters(array $parameters, Set $presets): Set
    {
        $values = [];
        foreach ($parameters as $parameter) {
            $field = $this->casedField($parameter);
            $generated = (new FromDependency($this->notation))
                ->then(new FromTypeDate($this->notation))
                ->then(new FromCollection($this->notation))
                ->then(new FromTypeBuiltin($this->notation))
                ->then(new FromTypeAttributes($this->notation))
                ->then(new FromEnum($this->notation))
                ->then(new FromDefaultValue($this->notation))
                ->then(new FromPreset($this->notation))
                ->resolve($parameter, $presets);

            if ($generated === null) {
                continue;
            }
            $values[$field] = $generated->content;
        }
        return Set::createFrom($values);
    }

    private function locale(?string $locale): string
    {
        $fallback = static function (string $default = 'en_US'): string {
            $locale = stringify(getenv('FAKER_LOCALE'), $default);
            return empty($locale) ? $default : $locale;
        };
        return $locale ?? $fallback();
    }
}
