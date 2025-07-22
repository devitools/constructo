<?php

declare(strict_types=1);

namespace Constructo\Test\Stub;

use Constructo\Test\Stub\Type\BackedEnumeration;
use Constructo\Test\Stub\Type\Enumeration;

class EnumVariety
{
    public function __construct(
        public readonly Enumeration $enum,
        public readonly BackedEnumeration $backed,
        public readonly BackedEnumeration|Enumeration $union,
        public readonly BackedEnumeration&Enumeration $intersection,
    ) {
    }
}
