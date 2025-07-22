<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Type;

use Closure;
use Constructo\Contract\Testing\Faker;
use Constructo\Support\Reflective\Definition\TypeExtended;
use Constructo\Support\Value;

use function Constructo\Cast\stringify;
use function Constructo\Crypt\decrypt;
use function Constructo\Crypt\encrypt;

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
