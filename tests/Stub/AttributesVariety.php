<?php

declare(strict_types=1);

namespace Morph\Test\Stub;

use DateTimeImmutable;
use SensitiveParameter;
use Morph\Support\Reflective\Attribute\Define;
use Morph\Support\Reflective\Attribute\Managed;
use Morph\Support\Reflective\Attribute\Pattern;
use Morph\Support\Reflective\Definition\Type;
use Morph\Test\Stub\Type\Sensitive;

readonly class AttributesVariety
{
    public function __construct(
        #[Managed('id')]
        public int $id,
        #[Managed('timestamp')]
        public DateTimeImmutable $createdAt,
        #[Define(Type::EMAIL)]
        public string $email,
        #[Pattern('/^[A-Z0-9]+$/')]
        public string $code,
        #[Pattern('/^\d+(\.\d{1,2})?$/')]
        public float $amount,
        #[Pattern('/^\d{2}$/')]
        public int $precision,
        #[Pattern('/^\d+$/')]
        public int|string $variant,
        #[SensitiveParameter]
        public string $cpf,
        #[Define(new Sensitive())]
        public string $sensitive,
        public mixed $noAttribute,
        $notTyped,
    ) {
    }
}
