<?php

declare(strict_types=1);

namespace Constructo\Exception\Adapter;

final readonly class NotResolvedCollection
{
    public function __construct(
        /**
         * @var NotResolved[]
         */
        public array $notResolved,
        /**
         * @var string[]
         */
        public array $path,
        public mixed $value = null,
    ) {
    }
}
