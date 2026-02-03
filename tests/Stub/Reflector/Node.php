<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Reflector;

class Node
{
    public function __construct(
        public readonly string $name,
        public readonly ?NodeCollection $children = null,
    ) {
    }
}
