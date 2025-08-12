<?php

declare(strict_types=1);

namespace Constructo\Test\Stub;

use Constructo\Test\Stub\Type\BackedEnumeration;
use Constructo\Test\Stub\Type\Enumeration;

readonly class EnumerationAndNullable
{
    public function __construct(
        public Enumeration $unit,
        public BackedEnumeration $backed,
        public ?Builtin $builtin,
        public ?array $drivers,
    ) {
    }
}
