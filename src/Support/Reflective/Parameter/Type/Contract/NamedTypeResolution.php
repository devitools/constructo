<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Parameter\Type\Contract;

enum NamedTypeResolution
{
    case Resolved;

    case NotResolved;
}
