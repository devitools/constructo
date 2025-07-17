<?php

declare(strict_types=1);

namespace Morph\Exception;

use InvalidArgumentException;
use Morph\Exception\Adapter\NotResolved;
use Morph\Support\Set;
use Throwable;

use function array_map;
use function count;
use function implode;
use function sprintf;

final class AdapterException extends InvalidArgumentException
{
    public function __construct(
        public readonly Set $values,
        /**
         * @var NotResolved[] $unresolved
         */
        private readonly array $unresolved = [],
        ?Throwable $error = null,
    ) {
        parent::__construct(
            message: $this->extractMessageFrom($unresolved, $error),
            previous: $error,
        );
    }

    /**
     * @return NotResolved[]
     */
    public function getUnresolved(): array
    {
        return $this->unresolved;
    }

    /**
     * @param NotResolved[] $notResolved
     */
    private function extractMessageFrom(array $notResolved, ?Throwable $error = null): string
    {
        if ($error === null) {
            return sprintf(
                'Adapter failed with %d error(s). The errors are: "%s"',
                count($notResolved),
                implode('", "', $this->merge($notResolved)),
            );
        }
        return $error->getMessage();
    }

    /**
     * @param NotResolved[] $errors
     * @return string[]
     */
    private function merge(array $errors): array
    {
        return array_map(fn (NotResolved $error) => $error->message, $errors);
    }
}
