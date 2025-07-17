<?php

declare(strict_types=1);

namespace Morph\Support;

readonly class Value
{
    public function __construct(public mixed $content)
    {
    }
}
