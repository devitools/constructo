<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Schema\Parameter\Field\Contract;

enum NamedTypeResolution
{
    case Resolved;

    case NotResolved;
}
