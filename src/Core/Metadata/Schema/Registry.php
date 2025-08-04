<?php

declare(strict_types=1);

namespace Constructo\Core\Metadata\Schema;

use Constructo\Contract\Formatter;
use Constructo\Core\Metadata\Schema\Registry\Spec;
use Constructo\Support\Set;
use InvalidArgumentException;

use function class_exists;
use function Constructo\Cast\stringify;
use function Constructo\Notation\snakify;
use function gettype;
use function is_string;
use function sprintf;

class Registry
{
    private array $specs = [];
    private readonly array $types;

    public function __construct(array $types = [])
    {
        $this->types = array_merge($this->defaultTypes(), $types);
    }

    public function getSpec(string $name): ?Spec
    {
        $name = snakify($name);
        $spec = $this->specs[$name] ?? null;
        if ($spec instanceof Spec) {
            return $spec;
        }
        return null;
    }

    public function registerSpec(string $name, array $data): void
    {
        $name = snakify($name);
        $properties = Set::createFrom($data);
        $formatter = $this->defineFormatter($properties);

        $spec = new Spec($name, $properties, $formatter);
        $this->specs[$name] = $spec;
    }

    public function hasSpec(string $name): bool
    {
        $name = snakify($name);
        return isset($this->specs[$name]);
    }

    public function getType(string $source): ?string
    {
        $type = $this->types[$source] ?? null;
        return $type
            ? stringify($type)
            : null;
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
        $instance = new $formatter();
        if (! $instance instanceof Formatter) {
            $given = gettype($instance);
            throw new InvalidArgumentException(
                sprintf('Formatter must implement %s, %s given.', Formatter::class, $given)
            );
        }
        return $instance;
    }

    protected function defaultTypes(): array
    {
        return [
            'DateTime' => 'date',
            'DateTimeImmutable' => 'date',
            'DateTimeInterface' => 'date',
        ];
    }
}
