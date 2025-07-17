<?php

declare(strict_types=1);

namespace Morph\Support\Reflective\Definition;

use Closure;
use Morph\Contract\Testing\Faker;
use Morph\Support\Value;

interface TypeExtended
{
    public function build(mixed $value, Closure $build): mixed;

    public function demolish(mixed $value, Closure $demolish): mixed;

    public function fake(Faker $faker): ?Value;
}
