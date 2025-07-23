<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Domain\Entity\Command;

use Constructo\Support\Entity;
use Constructo\Test\Stub\Domain\Collection\Game\FeatureCollection;
use Constructo\Type\Timestamp;

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
