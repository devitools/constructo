<?php

declare(strict_types=1);

namespace Morph\Test\Stub;

class Builtin
{
    public function __construct(
        public readonly string $string,
        public readonly int $int,
        public readonly float $float,
        public readonly bool $bool,
        public readonly array $array,
        public readonly null $null,
    ) {
    }
}
