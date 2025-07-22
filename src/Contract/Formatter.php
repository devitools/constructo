<?php

declare(strict_types=1);

namespace Constructo\Contract;

interface Formatter
{
    public function format(mixed $value, mixed $option = null): mixed;
}
