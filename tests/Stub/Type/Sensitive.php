<?php

declare(strict_types=1);

namespace Morph\Test\Stub\Type;

use Closure;
use Morph\Contract\Testing\Faker;
use Morph\Support\Reflective\Definition\TypeExtended;
use Morph\Support\Value;

use function Morph\Cast\stringify;
use function Morph\Crypt\decrypt;
use function Morph\Crypt\encrypt;

class Sensitive implements TypeExtended
{
    public function build(mixed $value, Closure $build): string
    {
        return decrypt(stringify($value));
    }

    public function demolish(mixed $value, Closure $demolish): string
    {
        return encrypt(stringify($value));
    }

    public function fake(Faker $faker): ?Value
    {
        $value = $faker->generate(
            'regexify',
            ['/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()\-_=+<>?]).{8,}$']
        );
        return new Value(encrypt($value));
    }
}
