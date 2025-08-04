<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Reflective\Engine;

use Constructo\Contract\Formatter;

class TestFormatter implements Formatter
{
    public function format(mixed $value, mixed $option = null): mixed
    {
        return $value;
    }
}
