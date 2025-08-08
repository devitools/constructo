<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Metadata\Schema\Field;

use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Field\Fieldset;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use PHPUnit\Framework\TestCase;

class FieldsetTest extends TestCase
{
    private Fieldset $fieldset;
    private Field $field1;
    private Field $field2;

    protected function setUp(): void
    {
        $this->fieldset = new Fieldset();

        $registry = $this->createMock(Specs::class);

        $rules1 = new Rules();
        $rules2 = new Rules();

        $this->field1 = new Field($registry, $rules1, 'test_field_1');
        $this->field2 = new Field($registry, $rules2, 'test_field_2');
    }

    public function testCanAddFieldToFieldset(): void
    {
        $this->fieldset->add('field1', $this->field1);

        $this->assertTrue($this->fieldset->has('field1'));
    }

    public function testCanGetExistingField(): void
    {
        $this->fieldset->add('field1', $this->field1);

        $retrievedField = $this->fieldset->get('field1');

        $this->assertSame($this->field1, $retrievedField);
    }

    public function testGetNonExistentFieldReturnsNull(): void
    {
        $result = $this->fieldset->get('nonexistent');

        $this->assertNull($result);
    }

    public function testHasReturnsTrueForExistingField(): void
    {
        $this->fieldset->add('field1', $this->field1);

        $this->assertTrue($this->fieldset->has('field1'));
    }

    public function testHasReturnsFalseForNonExistentField(): void
    {
        $this->assertFalse($this->fieldset->has('nonexistent'));
    }

    public function testCanAddMultipleFields(): void
    {
        $this->fieldset->add('field1', $this->field1);
        $this->fieldset->add('field2', $this->field2);

        $this->assertTrue($this->fieldset->has('field1'));
        $this->assertTrue($this->fieldset->has('field2'));
        $this->assertSame($this->field1, $this->fieldset->get('field1'));
        $this->assertSame($this->field2, $this->fieldset->get('field2'));
    }

    public function testAddingFieldWithSameNameOverwritesPrevious(): void
    {
        $this->fieldset->add('field1', $this->field1);
        $this->fieldset->add('field1', $this->field2);

        $retrievedField = $this->fieldset->get('field1');

        $this->assertSame($this->field2, $retrievedField);
        $this->assertNotSame($this->field1, $retrievedField);
    }

    public function testFilterReturnsMatchingFields(): void
    {
        $this->fieldset->add('field1', $this->field1);
        $this->fieldset->add('field2', $this->field2);

        $filtered = $this->fieldset->filter(fn (Field $field) => $field->name === 'test_field_1');

        $this->assertCount(1, $filtered);
        $this->assertContains($this->field1, $filtered);
        $this->assertNotContains($this->field2, $filtered);
    }

    public function testFilterReturnsEmptyArrayWhenNoMatches(): void
    {
        $this->fieldset->add('field1', $this->field1);
        $this->fieldset->add('field2', $this->field2);

        $filtered = $this->fieldset->filter(fn (Field $field) => $field->name === 'nonexistent');

        $this->assertEmpty($filtered);
    }

    public function testFilterReturnsAllFieldsWhenAllMatch(): void
    {
        $this->fieldset->add('field1', $this->field1);
        $this->fieldset->add('field2', $this->field2);

        $filtered = $this->fieldset->filter(fn (Field $field) => true);

        $this->assertCount(2, $filtered);
        $this->assertContains($this->field1, $filtered);
        $this->assertContains($this->field2, $filtered);
        $this->assertSame($filtered, $this->fieldset->all());
    }
}
