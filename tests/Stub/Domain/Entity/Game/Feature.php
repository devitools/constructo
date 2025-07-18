<?php

declare(strict_types=1);

namespace Morph\Test\Stub\Domain\Entity\Game;

use Morph\Support\Entity;
use Morph\Support\Reflective\Attribute\Define;
use Morph\Support\Reflective\Definition\Type;

class Feature extends Entity
{
    public function __construct(
        #[Define(Type::JOB_TITLE)]
        public readonly string $name,
        #[Define(Type::SENTENCE)]
        public readonly string $description,
        public readonly bool $enabled,
    ) {
    }
}
