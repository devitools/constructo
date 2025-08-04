<?php

declare(strict_types=1);

namespace Constructo\Testing;

use Constructo\Support\Managed;

trait ManagedExtension
{
    private ?Managed $managed = null;

    protected function managed(): Managed
    {
        if ($this->managed === null) {
            $this->managed = $this->make(class: Managed::class);
        }
        return $this->managed;
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
