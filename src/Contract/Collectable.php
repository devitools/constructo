<?php

declare(strict_types=1);

namespace Constructo\Contract;

use Closure;

interface Collectable
{
    public function all(): array;

    public function push(object $datum): void;

    public function map(Closure $param): mixed;
}
