<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Attribute;

use Attribute;

#[Attribute]
readonly class Pattern
{
    public function __construct(public string $pattern = '/^.{0,255}$/')
    {
    }
}
