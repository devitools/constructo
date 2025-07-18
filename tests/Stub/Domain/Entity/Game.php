<?php

declare(strict_types=1);

namespace Morph\Test\Stub\Domain\Entity;

use Morph\Support\Reflective\Attribute\Managed;
use Morph\Support\Reflective\Attribute\Pattern;
use Morph\Type\Timestamp;
use Morph\Test\Stub\Domain\Collection\Game\FeatureCollection;
use Morph\Test\Stub\Domain\Entity\Command\GameCommand;

class Game extends GameCommand
{
    public function __construct(
        #[Managed('id')]
        public readonly string $id,
        #[Managed('timestamp')]
        public readonly Timestamp $createdAt,
        #[Managed('timestamp')]
        public readonly Timestamp $updatedAt,
        #[Pattern('/^[a-zA-Z]{1,255}$/')]
        string $name,
        string $slug,
        Timestamp $publishedAt,
        array $data,
        FeatureCollection $features,
    ) {
        parent::__construct(
            name: $name,
            slug: $slug,
            publishedAt: $publishedAt,
            data: $data,
            features: $features,
        );
    }
}
