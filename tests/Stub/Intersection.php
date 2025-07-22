<?php

declare(strict_types=1);

namespace Constructo\Test\Stub;

use Countable;
use Iterator;

class Intersection
{
    public function __construct(public readonly Countable&Iterator $intersected)
    {
    }
}
