<?php

declare(strict_types=1);

namespace Constructo\Core\Metadata\Schema\Element;

use InvalidArgumentException;
use Constructo\Contract\Formatter;
use Constructo\Support\Set;

use function class_exists;
use function gettype;
use function is_string;
use function Serendipity\Notation\snakify;
use function Constructo\Cast\stringify;
use function sprintf;

class SchemaRegistry
{
    private array $specs = [];

    public function __construct(private readonly array $types = [])
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

    public function defineFormatter(Set $properties): ?Formatter
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

    public function has(string $name): bool
    {
        $name = snakify($name);
        return isset($this->specs[$name]);
    }

    public function type(string $source): ?string
    {
        $type = $this->types[$source] ?? null;
        return $type
            ? stringify($type)
            : null;
    }
}
