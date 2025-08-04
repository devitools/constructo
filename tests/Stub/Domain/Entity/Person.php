<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Domain\Entity;

use Constructo\Support\Reflective\Attribute\Managed;
use Constructo\Test\Stub\Domain\Entity\Command\PersonCommand;

class Person extends PersonCommand
{
    public function __construct(
        #[Managed('auto-increment')]
        public readonly int $id,
        string $name,
        ?Person $mom,
        ?Person $dad = null,
    ) {
        parent::__construct($name, $mom, $dad);
    }
}
