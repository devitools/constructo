<?php

declare(strict_types=1);

namespace Morph\Contract;

interface Formatter
{
    public function format(mixed $value, mixed $option = null): mixed;
}
