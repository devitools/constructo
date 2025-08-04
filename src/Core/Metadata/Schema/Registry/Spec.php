<?php

declare(strict_types=1);

namespace Constructo\Core\Metadata\Schema\Registry;

use Constructo\Contract\Formatter;
use Constructo\Support\Set;

readonly class Spec
{
    public function __construct(
        public string $name,
        public Set $properties,
        public ?Formatter $formatter,
    ) {
    }
}
