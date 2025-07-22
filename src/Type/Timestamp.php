<?php

declare(strict_types=1);

namespace Constructo\Type;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;

class Timestamp extends DateTimeImmutable implements JsonSerializable
{
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->format(DateTimeInterface::ATOM);
    }
}
