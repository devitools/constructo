<?php

declare(strict_types=1);

namespace Morph\Contract;

use Morph\Support\Set;

interface Message
{
    public function properties(): Set;

    public function content(): mixed;
}
