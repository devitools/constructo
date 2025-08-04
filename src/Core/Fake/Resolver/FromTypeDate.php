<?php

declare(strict_types=1);

namespace Constructo\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver;
use Constructo\Support\Set;
use Constructo\Support\Value;
use Constructo\Testing\MakeExtension;
use Constructo\Testing\ManagedExtension;
use Constructo\Type\Timestamp;
use DateMalformedStringException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionParameter;

final class FromTypeDate extends Resolver
{
    use MakeExtension;
    use ManagedExtension;

    /**
     * @throws DateMalformedStringException
     */
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        $type = $this->detectReflectionType($parameter->getType());
        if ($type === null) {
            return parent::resolve($parameter, $presets);
        }

        return $this->resolveByClassName($type)
            ?? parent::resolve($parameter, $presets);
    }

    /**
     * @throws DateMalformedStringException
     */
    private function resolveByClassName(string $type): ?Value
    {
        $now = $this->now();
        return match ($type) {
            Timestamp::class => new Value(new Timestamp($now)),
            DateTimeImmutable::class => new Value(new DateTimeImmutable($now)),
            DateTime::class,
            DateTimeInterface::class => new Value(new DateTime($now)),
            default => null,
        };
    }

    private function now(): string
    {
        return $this->generator->dateTime()->format(DateTimeInterface::ATOM);
    }
}
