<?php

declare(strict_types=1);

namespace Constructo\Type;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use JsonSerializable;
use Stringable;

class Timestamp extends DateTimeImmutable implements JsonSerializable, Stringable
{
    private readonly string $pattern;

    public function __construct(string $datetime = 'now', ?DateTimeZone $timezone = null, ?string $pattern = null)
    {
        parent::__construct($datetime, $timezone);

        $this->pattern = $pattern ?? DateTimeInterface::ATOM;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->format($this->pattern);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
