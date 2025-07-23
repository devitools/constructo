<?php

declare(strict_types=1);

namespace Constructo\Test\Support;

use Constructo\Exception\SchemaException;
use Constructo\Support\Set;
use PHPUnit\Framework\TestCase;

final class SetTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $values = Set::createFrom(['key' => 'value']);
        $this->assertEquals('value', $values->get('key'));
    }

    public function testGetExistingKey(): void
    {
        $values = new Set(['key' => 'value']);
        $this->assertEquals('value', $values->get('key'));
    }

    public function testGetNonExistingKey(): void
    {
        $values = new Set(['key' => 'value']);
        $this->assertNull($values->get('non_existing_key'));
    }

    public function testWithNewValue(): void
    {
        $values = new Set(['key' => 'value']);
        $newValues = $values->with('new_key', 'new_value');
        $this->assertEquals('new_value', $newValues->get('new_key'));
        $this->assertEquals('value', $newValues->get('key'));
    }

    public function testAlongWithValues(): void
    {
        $values = new Set(['key' => 'value']);
        $newValues = $values->along(['new_key' => 'new_value']);
        $this->assertEquals('new_value', $newValues->get('new_key'));
        $this->assertEquals('value', $newValues->get('key'));
    }

    public function testHasKey(): void
    {
        $values = new Set(['key' => 'value']);
        $this->assertTrue($values->has('key'));
        $this->assertFalse($values->has('non_existing_key'));
    }

    public function testCopyValues(): void
    {
        $values = new Set(['key' => 'value']);
        $copy = $values->toArray();
        $this->assertIsArray($copy);
        $this->assertEquals('value', $copy['key']);
    }

    public function testInvalidValuesArray(): void
    {
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('Values must be an array.');
        new Set('invalid');
    }

    public function testInvalidKeysInArray(): void
    {
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('All keys must be strings.');
        new Set(['value', 5 => 'foo', 'key' => 'value']);
    }

    public function testAtExistingKey(): void
    {
        $values = new Set(['key' => 'value']);
        $this->assertEquals('value', $values->at('key'));
    }

    public function testAtNonExistingKey(): void
    {
        $values = new Set(['key' => 'value']);
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage("Field 'non_existing_key' not found.");
        $values->at('non_existing_key');
    }
}
