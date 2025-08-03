<?php

declare(strict_types=1);

namespace Constructo\Testing\Extension;

use Constructo\Core\Serialize\Builder;

trait BuilderExtension
{
    private ?Builder $builder = null;

    protected function builder(): Builder
    {
        if ($this->builder === null) {
            $this->builder = $this->make(Builder::class);
        }
        return $this->builder;
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
