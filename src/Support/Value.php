<?php

declare(strict_types=1);

namespace Constructo\Support;

readonly class Value
{
    public function __construct(public mixed $content)
    {
    }
}
