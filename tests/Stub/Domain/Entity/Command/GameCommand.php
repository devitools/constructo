<?php

declare(strict_types=1);

namespace Morph\Test\Stub\Domain\Entity\Command;

use Morph\Support\Entity;
use Morph\Type\Timestamp;
use Morph\Test\Stub\Domain\Collection\Game\FeatureCollection;

class GameCommand extends Entity
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly Timestamp $publishedAt,
        public readonly array $data,
        public readonly FeatureCollection $features,
    ) {
    }
}
