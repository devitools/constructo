<?php

declare(strict_types=1);

namespace Constructo\Support;

use Constructo\Contract\Exportable;
use Error;
use JsonSerializable;
use Throwable;

class Datum implements Exportable, JsonSerializable
{
    private readonly Set $set;

    public function __construct(
        private readonly Throwable $throwable,
        array $data
    ) {
        $this->set = Set::createFrom($data);
    }

    public function export(): object
    {
        # test 4
        return (object) [
            ...$this->set->toArray(),
            '@error' => [
                'message' => $this->throwable->getMessage(),
                'code' => $this->throwable->getCode(),
                'file' => $this->throwable->getFile(),
                'line' => $this->throwable->getLine(),
            ],
        ];
    }

    public function jsonSerialize(): object
    {
        return $this->export();
    }

    public function __set(string $name, mixed $value): void
    {
        throw new Error(sprintf('Cannot modify readonly property %s::%s', static::class, $name));
    }

    public function __get(string $name): mixed
    {
        return $this->set->get($name);
    }

    public function __isset(string $name): bool
    {
        return $this->set->has($name);
    }
}
