<?php

declare(strict_types=1);

namespace Morph\Test\Stub;

use DateTime;
use Morph\Support\Entity;
use Morph\Test\Stub\Type\SingleBacked;

class EntityStub extends Entity
{
    public function __construct(
        public readonly int $id,
        public readonly float $price,
        public readonly string $name,
        public readonly bool $isActive,
        public readonly NoConstructor $more,
        public readonly ?DateTime $createdAt,
        public readonly ?NoParameters $no,
        public readonly array $tags = [],
        public readonly SingleBacked $enum = SingleBacked::ONE,
        ?string $foo = null,
    ) {
    }
}
