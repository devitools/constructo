<?php

declare(strict_types=1);

namespace Constructo\Test\Stub;

use DateTimeImmutable;
use Constructo\Support\Entity;
use SensitiveParameter;
use Constructo\Support\Reflective\Attribute\Define;
use Constructo\Support\Reflective\Attribute\Pattern;
use Constructo\Support\Reflective\Definition\Type;
use Constructo\Test\Stub\Type\Gender;
use Constructo\Test\Stub\Type\Sensitive;

class Command extends Entity
{
    /**
     * @SuppressWarnings(ExcessiveParameterList)
     * @SuppressWarnings(ShortVariable)
     */
    public function __construct(
        #[Define(Type::EMAIL)]
        public readonly string $email,
        #[Define(Type::IP_V4, Type::IP_V6)]
        public readonly string $ipAddress,
        public readonly DateTimeImmutable $signupDate,
        public readonly Gender $gender,
        #[Define(Type::FIRST_NAME)]
        public readonly string $firstName,
        #[Define(new Sensitive())]
        public readonly string $password,
        #[SensitiveParameter]
        public readonly ?string $address = null,
        #[Pattern('/^[a-zA-Z]{1,255}$/')]
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $zip = null,
        public readonly ?string $phone = null,
        public readonly ?string $leadId = null,
        public readonly ?string $birthday = null,
        public readonly ?DateTimeImmutable $dob = null,
        public readonly ?string $c1 = null,
        public readonly ?string $hid = null,
        public readonly ?string $carMake = null,
        public readonly ?string $carModel = null,
        public readonly ?int $carYear = null,
    ) {
    }
}
