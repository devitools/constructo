<?php

declare(strict_types=1);

namespace Constructo\Support;

use Constructo\Contract\Managed\IdGenerator;
use Constructo\Exception\ManagedException;
use Constructo\Support\Managed\Cuid2IdGenerator;
use Constructo\Type\Timestamp;

readonly class Managed
{
    private IdGenerator $idGenerator;

    public function __construct(
        int $length = 10,
        ?IdGenerator $idGenerator = null,
    ) {
        $this->idGenerator = $idGenerator ?? new Cuid2IdGenerator($length);
    }

    /**
     * @throws ManagedException
     */
    public function id(): string
    {
        return $this->idGenerator->generate();
    }

    public function now(): string
    {
        return (new Timestamp())->toString();
    }
}
