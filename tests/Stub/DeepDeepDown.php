<?php

declare(strict_types=1);

namespace Morph\Test\Stub;

use Morph\Test\Stub\Type\EmptyEnum;

final readonly class DeepDeepDown
{
    public function __construct(
        public EntityStub $stub,
        public Builtin $builtin,
        public EmptyEnum $empty,
    ) {
    }
}
