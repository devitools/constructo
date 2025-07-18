<?php

declare(strict_types=1);

namespace Morph\Test\Stub;

class Complex
{
    public function __construct(
        public readonly EntityStub $entity,
        public readonly Native $native,
        public readonly Builtin $builtin,
    ) {
    }
}
