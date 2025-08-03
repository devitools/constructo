<?php

declare(strict_types=1);

namespace Constructo\Core\Metadata;

use Constructo\Core\Metadata\Schema\Element\Rules;
use Constructo\Core\Metadata\Schema\Element\SchemaRegistry;
use Constructo\Core\Metadata\Schema\Field;
use Constructo\Core\Metadata\Schema\Fieldset;
use InvalidArgumentException;

use function array_map;

final readonly class Schema
{
    public function __construct(
        private SchemaRegistry $registry,
        private Fieldset $fieldset,
    ) {
    }

    public function add(string $name): Field
    {
        $field = $this->fieldset->get($name);
        if ($field !== null) {
            return $field;
        }

        $field = new Field($this->registry, new Rules(), $name);
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

    public function rules(): array
    {
        return array_map(fn (Field $field) => $field->rules(), $this->available());
    }

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

    private function available(): array
    {
        return $this->fieldset->filter(fn (Field $field) => $field->isAvailable());
    }
}
