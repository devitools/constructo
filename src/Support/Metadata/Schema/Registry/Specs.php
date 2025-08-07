<?php

declare(strict_types=1);

namespace Constructo\Support\Metadata\Schema\Registry;

use Constructo\Contract\Formatter;
use Constructo\Core\Serialize\Builder;
use Constructo\Support\Set;
use InvalidArgumentException;

use function class_exists;
use function Constructo\Notation\snakify;
use function gettype;
use function is_string;
use function sprintf;

class Specs
{
    private array $specs = [];

    public function __construct(private readonly Builder $builder)
    {
    }

    public function get(string $name): ?Spec
    {
        $name = snakify($name);
        $spec = $this->specs[$name] ?? null;
        if ($spec instanceof Spec) {
            return $spec;
        }
        return null;
    }

    public function register(string $name, array $data): void
    {
        $name = snakify($name);
        $properties = Set::createFrom($data);
        $formatter = $this->defineFormatter($properties);

        $spec = new Spec($name, $properties, $formatter);
        $this->specs[$name] = $spec;
    }

    public function has(string $name): bool
    {
        $name = snakify($name);
        return isset($this->specs[$name]);
    }

    protected function defineFormatter(Set $properties): ?Formatter
    {
        $formatter = $properties->get('formatter');
        if ($formatter === null) {
            return null;
        }
        if (! is_string($formatter) || ! class_exists($formatter)) {
            $given = gettype($formatter);
            throw new InvalidArgumentException(sprintf('Formatter must be a valid class-string, %s given.', $given));
        }
        return $this->createFormatter($formatter);
    }

    /**
     * @param class-string<object> $formatter
     */
    private function createFormatter(string $formatter): Formatter
    {
        $instance = $this->builder->build($formatter);
        if (! $instance instanceof Formatter) {
            $given = gettype($instance);
            throw new InvalidArgumentException(
                sprintf('Formatter must implement %s, %s given.', Formatter::class, $given)
            );
        }
        return $instance;
    }
}
