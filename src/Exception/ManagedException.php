<?php

declare(strict_types=1);

namespace Constructo\Exception;

use Exception;
use Throwable;

use function sprintf;

final class ManagedException extends Exception
{
    public function __construct(
        public readonly string $type,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message: sprintf('Error generating "%s": "%s"', $type, $previous?->getMessage() ?? 'unknown'),
            previous: $previous
        );
    }
}
