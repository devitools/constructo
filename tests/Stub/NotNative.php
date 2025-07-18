<?php

declare(strict_types=1);

namespace Morph\Test\Stub;

use Morph\Test\Stub\Type\BackedEnumeration;
use Morph\Test\Stub\Type\Enumeration;

class NotNative
{
    public function __construct(
        public readonly BackedEnumeration $backed,
        public readonly Enumeration $enum,
        public readonly Stub $stub,
    ) {
    }
}
