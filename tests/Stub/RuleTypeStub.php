<?php

declare(strict_types=1);

namespace Constructo\Test\Stub;

class RuleTypeStub
{
    public function __construct(
        public readonly array $array,
        public readonly bool $bool,
        public readonly int $int,
        public readonly float $float,
        public readonly string $string,
        public readonly mixed $mixed,
    ) {
    }
}
