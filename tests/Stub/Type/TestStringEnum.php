<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Type;

enum TestStringEnum: string
{
    case FIRST = 'first';
    case SECOND = 'second';
    case THIRD = 'third';
}
