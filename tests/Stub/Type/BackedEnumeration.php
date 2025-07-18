<?php

declare(strict_types=1);

namespace Morph\Test\Stub\Type;

enum BackedEnumeration: string
{
    case FOO = 'foo';
    case BAR = 'bar';
    case BAZ = 'baz';
}
