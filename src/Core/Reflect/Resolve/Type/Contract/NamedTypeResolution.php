<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Resolve\Type\Contract;

enum NamedTypeResolution
{
    case Resolved;

    case NotResolved;
}
