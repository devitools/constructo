<?php

declare(strict_types=1);

namespace Constructo\Test\Stub;

use Constructo\Support\Entity;
use Constructo\Support\Reflective\Attribute\Pattern;

class PatternMock extends Entity
{
    public function __construct(
        #[Pattern('/^[0-9]{1,20}$/')]
        public readonly int $id,
        #[Pattern('/^[a-zA-Z]{1,255}$/')]
        public readonly string $name,
        #[Pattern('/^[0-9]{1,20}$/')]
        public readonly int|string $code,
        #[Pattern('/^[+-]?((\d+\.?\d*)|(\.\d+))$/')]
        public readonly float $amount,
    ) {
    }
}
