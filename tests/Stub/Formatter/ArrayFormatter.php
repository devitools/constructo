<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Formatter;


use Constructo\Contract\Formatter;

use function is_array;
use function is_string;
use function Constructo\Json\decode;

class ArrayFormatter implements Formatter
{
    public function format(mixed $value, mixed $option = null): ?array
    {
        if (is_array($value)) {
            return $value;
        }
        if (! is_string($value)) {
            return null;
        }
        return decode($value);
    }
}
