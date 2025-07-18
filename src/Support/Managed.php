<?php

declare(strict_types=1);

namespace Morph\Support;

use Morph\Exception\ManagedException;
use Morph\Type\Timestamp;
use Throwable;
use Visus\Cuid2\Cuid2;

class Managed
{
    public function __construct(public readonly int $length = 10)
    {
    }

    /**
     * @throws ManagedException
     */
    public function id(): string
    {
        try {
            return (new Cuid2($this->length))->toString();
        } catch (Throwable $exception) {
            throw new ManagedException('id', $exception);
        }
    }

    public function now(): string
    {
        return (new Timestamp())->toString();
    }
}
