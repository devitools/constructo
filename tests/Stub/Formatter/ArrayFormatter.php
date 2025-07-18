<?php

declare(strict_types=1);

namespace Morph\Test\Stub\Formatter;


use Morph\Contract\Formatter;

use function is_array;
use function is_string;
use function Morph\Json\decode;

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
