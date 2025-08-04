<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Resolver\Type\Contract;

enum NamedTypeResolution
{
    case Resolved;

    case NotResolved;
}
