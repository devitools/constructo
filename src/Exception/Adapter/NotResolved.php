<?php

declare(strict_types=1);

namespace Constructo\Exception\Adapter;

final readonly class NotResolved
{
    public function __construct(
        public string $message,
        public string $field = '',
        public mixed $value = null,
    ) {
    }
}
