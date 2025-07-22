<?php

declare(strict_types=1);

namespace Constructo\Contract;

use Constructo\Support\Set;

interface Message
{
    public function properties(): Set;

    public function content(): mixed;
}
