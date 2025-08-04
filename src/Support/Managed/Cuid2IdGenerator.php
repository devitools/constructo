<?php

declare(strict_types=1);

namespace Constructo\Support\Managed;

use Constructo\Contract\Managed\IdGenerator;
use Constructo\Exception\ManagedException;
use Throwable;
use Visus\Cuid2\Cuid2;

readonly class Cuid2IdGenerator implements IdGenerator
{
    public function __construct(private int $length = 10)
    {
    }

    public function generate(): string
    {
        try {
            return (new Cuid2($this->length))->toString();
        } catch (Throwable $exception) {
            throw new ManagedException('id', $exception);
        }
    }
}
