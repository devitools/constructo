<?php

declare(strict_types=1);

namespace Constructo\Test\Stub;

final readonly class DeepDown
{
    public function __construct(
        public DeepDeepDown $deepDeepDown,
        public Builtin $builtin,
    ) {
    }
}
