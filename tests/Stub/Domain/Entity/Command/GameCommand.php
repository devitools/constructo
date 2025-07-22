<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Domain\Entity\Command;

use Constructo\Support\Entity;
use Constructo\Type\Timestamp;
use Constructo\Test\Stub\Domain\Collection\Game\FeatureCollection;

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
