<?php

declare(strict_types=1);

namespace Constructo\Support\Metadata\Schema\Field;

use Closure;
use Constructo\Support\Metadata\Schema\Field;

class Fieldset
{
    /**
     * @var array<string, Field>
     */
    private array $fields = [];

    public function get(string $name): ?Field
    {
        return $this->fields[$name] ?? null;
    }

    public function add(string $name, Field $field): void
    {
        $this->fields[$name] = $field;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * @return array<string, Field>
     */
    public function filter(Closure $criteria): array
    {
        return array_filter($this->fields, $criteria);
    }

    /**
     * @return array<string, Field>
     */
    public function all(): array
    {
        return $this->fields;
    }
}
