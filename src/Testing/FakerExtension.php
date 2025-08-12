<?php

declare(strict_types=1);

namespace Constructo\Testing;

use Constructo\Core\Fake\Faker;
use Constructo\Support\Reflective\Notation;
use Faker\Generator;

trait FakerExtension
{
    private ?Faker $faker = null;

    protected function faker(
        Notation $case = Notation::SNAKE,
        array $formatters = [],
        ?string $locale = null,
        bool $ignoreFromDefaultValue = false,
    ): Faker
    {
        if ($this->faker === null) {
            $args = [
                'case' => $case,
                'formatters' => $formatters,
                'locale' => $locale,
                'ignoreFromDefaultValue' => $ignoreFromDefaultValue,
            ];
            $this->faker = $this->make(Faker::class, $args);
        }
        return $this->faker;
    }

    protected function generator(): Generator
    {
        return $this->faker()->generator();
    }

    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     *
     * @return T
     */
    abstract protected function make(string $class, array $args = []): mixed;
}
