<?php

declare(strict_types=1);

namespace Constructo\Contract\Managed;

use Constructo\Exception\ManagedException;

interface IdGenerator
{
    /**
     * @throws ManagedException
     */
    public function generate(): string;
}
