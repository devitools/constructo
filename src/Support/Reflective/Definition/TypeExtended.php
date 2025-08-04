<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Definition;

use Closure;
use Constructo\Contract\Testing\Faker;
use Constructo\Support\Value;

interface TypeExtended
{
    public function build(mixed $value, Closure $build): mixed;

    public function demolish(mixed $value, Closure $demolish): mixed;

    public function fake(Faker $faker): ?Value;

    public function rule(): ?string;
}
