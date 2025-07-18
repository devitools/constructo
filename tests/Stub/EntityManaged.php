<?php

declare(strict_types=1);

namespace Morph\Test\Stub;

use DateTimeImmutable;
use Morph\Support\Reflective\Attribute\Managed;

class EntityManaged
{
    public function __construct(
        #[Managed(management: 'id')]
        public readonly string $id,
        #[Managed(management: 'timestamp')]
        public readonly DateTimeImmutable $createdAt,
        #[Managed(management: 'timestamp')]
        public readonly DateTimeImmutable $updatedAt,
        public readonly string $name,
    ) {
    }
}
