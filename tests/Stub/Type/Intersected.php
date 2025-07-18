<?php

declare(strict_types=1);

namespace Morph\Test\Stub\Type;

use Countable;
use Iterator;
use RuntimeException;

class Intersected implements Iterator, Countable
{
    public function current(): mixed
    {
        throw new RuntimeException('Not implemented');
    }

    public function key(): mixed
    {
        throw new RuntimeException('Not implemented');
    }

    public function next(): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function rewind(): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function valid(): bool
    {
        throw new RuntimeException('Not implemented');
    }

    public function count(): int
    {
        throw new RuntimeException('Not implemented');
    }
}
