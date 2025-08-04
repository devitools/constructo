<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Domain\Entity\Command;

use Constructo\Test\Stub\Domain\Entity\Person;

class PersonCommand
{
    public function __construct(
        public readonly string $name,
        public readonly ?Person $mom,
        public readonly ?Person $dad = null,
        public readonly string|int $externalId = 'default',
    ) {
    }
}
