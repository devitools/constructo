<?php

declare(strict_types=1);

namespace Constructo\Core\Metadata\Schema\Registry;

use Constructo\Contract\Formatter;
use Constructo\Support\Set;

class Spec
{
    public function __construct(
        public readonly string $name,
        public readonly Set $properties,
        public readonly ?Formatter $formatter,
    ) {
    }
}
