<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Metadata\Schema\Registry;

use Constructo\Support\Metadata\Schema\Registry\Types;
use PHPUnit\Framework\TestCase;

final class TypesTest extends TestCase
{
    public function testConstructorWithDefaultTypes(): void
    {
        $types = new Types();

        $this->assertTrue($types->has('DateTime'));
        $this->assertTrue($types->has('DateTimeImmutable'));
        $this->assertTrue($types->has('DateTimeInterface'));
        $this->assertSame('date', $types->get('DateTime'));
        $this->assertSame('date', $types->get('DateTimeImmutable'));
        $this->assertSame('date', $types->get('DateTimeInterface'));
    }

    public function testConstructorWithCustomTypes(): void
    {
        $customTypes = [
            'CustomClass' => 'custom',
            'AnotherClass' => 'another',
        ];
        $types = new Types($customTypes);

        $this->assertTrue($types->has('CustomClass'));
        $this->assertTrue($types->has('AnotherClass'));
        $this->assertSame('custom', $types->get('CustomClass'));
        $this->assertSame('another', $types->get('AnotherClass'));
    }

    public function testConstructorMergesCustomTypesWithDefaults(): void
    {
        $customTypes = [
            'CustomClass' => 'custom',
            'DateTime' => 'overridden_date',
        ];
        $types = new Types($customTypes);

        $this->assertTrue($types->has('DateTime'));
        $this->assertTrue($types->has('DateTimeImmutable'));
        $this->assertTrue($types->has('CustomClass'));
        $this->assertSame('overridden_date', $types->get('DateTime'));
        $this->assertSame('date', $types->get('DateTimeImmutable'));
        $this->assertSame('custom', $types->get('CustomClass'));
    }

    public function testHasReturnsTrueForExistingType(): void
    {
        $types = new Types(['ExistingType' => 'existing']);

        $this->assertTrue($types->has('ExistingType'));
        $this->assertTrue($types->has('DateTime'));
    }

    public function testHasReturnsFalseForNonExistingType(): void
    {
        $types = new Types();

        $this->assertFalse($types->has('NonExistingType'));
        $this->assertFalse($types->has('UnknownClass'));
    }

    public function testGetReturnsStringForExistingType(): void
    {
        $types = new Types(['TestType' => 'test_value']);

        $this->assertSame('test_value', $types->get('TestType'));
        $this->assertSame('date', $types->get('DateTime'));
    }

    public function testGetReturnsNullForNonExistingType(): void
    {
        $types = new Types();

        $this->assertNull($types->get('NonExistingType'));
        $this->assertNull($types->get('UnknownClass'));
    }

    public function testGetStringifiesNonStringValues(): void
    {
        $types = new Types([
            'IntType' => 123,
            'FloatType' => 45.67,
            'BoolType' => true,
            'ArrayType' => ['value'],
        ]);

        $this->assertSame('123', $types->get('IntType'));
        $this->assertSame('45.67', $types->get('FloatType'));
        $this->assertSame('1', $types->get('BoolType'));
        $this->assertSame('', $types->get('ArrayType'));
    }

    public function testGetHandlesNullValues(): void
    {
        $types = new Types(['NullType' => null]);

        $this->assertNull($types->get('NullType'));
    }

    public function testEmptyConstructorArray(): void
    {
        $types = new Types([]);

        $this->assertTrue($types->has('DateTime'));
        $this->assertTrue($types->has('DateTimeImmutable'));
        $this->assertTrue($types->has('DateTimeInterface'));
        $this->assertFalse($types->has('CustomType'));
    }

    public function testMultipleCustomTypesOverrideDefaults(): void
    {
        $customTypes = [
            'DateTime' => 'custom_date',
            'DateTimeImmutable' => 'custom_immutable',
            'DateTimeInterface' => 'custom_interface',
        ];
        $types = new Types($customTypes);

        $this->assertSame('custom_date', $types->get('DateTime'));
        $this->assertSame('custom_immutable', $types->get('DateTimeImmutable'));
        $this->assertSame('custom_interface', $types->get('DateTimeInterface'));
    }
}
