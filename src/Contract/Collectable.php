<?php

declare(strict_types=1);

namespace Morph\Contract;

interface Collectable
{
    public function all(): array;

    public function push(object $datum): void;
}
