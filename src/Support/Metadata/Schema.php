<?php

declare(strict_types=1);

namespace Constructo\Support\Metadata;

use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Field\Fieldset;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use InvalidArgumentException;

use function array_map;

final readonly class Schema
{
    public function __construct(
        private Specs $specs,
        private Fieldset $fieldset,
    ) {
    }

    public function add(string $name): Field
    {
        $field = $this->fieldset->get($name);
        if ($field !== null) {
            return $field;
        }

        $field = new Field($this->specs, new Rules(), $name);
        $this->fieldset->add($name, $field);
        return $field;
    }

    public function get(string $name): Field
    {
        $field = $this->fieldset->get($name);
        if ($field === null) {
            throw new InvalidArgumentException(sprintf("Field '%s' does not exist in the schema.", $name));
        }
        return $field;
    }

    /**
     * @return array<string, array|string>
     */
    public function rules(): array
    {
        $callback = fn (Field $field) => $field->rules();
        $rules = $this->available();
        return array_map($callback, $rules);
    }

    /**
     * @return array<string, callable(mixed):mixed|string>
     */
    public function mappings(): array
    {
        $mappings = [];
        foreach ($this->available() as $name => $field) {
            $mapping = $field->mapping();
            if ($mapping === null) {
                continue;
            }
            $mappings[$name] = $mapping;
        }
        return $mappings;
    }

    /**
     * @return array<string, Field>
     */
    private function available(): array
    {
        return $this->fieldset->filter(fn (Field $field) => $field->isAvailable());
    }
}
