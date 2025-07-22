<?php

declare(strict_types=1);

namespace Constructo\Test\Stub;

use Constructo\Test\Stub\Type\BackedEnumeration;
use Constructo\Test\Stub\Type\Enumeration;

class NotNative
{
    public function __construct(
        public readonly BackedEnumeration $backed,
        public readonly Enumeration $enum,
        public readonly Stub $stub,
    ) {
    }
}
