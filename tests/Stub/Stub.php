<?php

declare(strict_types=1);

namespace Constructo\Test\Stub;

use Constructo\Test\Stub\Type\BackedEnumeration;

class Stub
{
    public function __construct(
        public readonly string $foo,
        public readonly int $bar,
        public readonly BackedEnumeration $baz = BackedEnumeration::BAZ,
    ) {
    }
}
