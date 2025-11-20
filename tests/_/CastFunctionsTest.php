<?php

declare(strict_types=1);

namespace Constructo\Test\_;

use PHPUnit\Framework\TestCase;
use stdClass;
use Stringable;

use function Constructo\Cast\arrayify;
use function Constructo\Cast\boolify;
use function Constructo\Cast\floatify;
use function Constructo\Cast\integerify;
use function Constructo\Cast\mapify;
use function Constructo\Cast\stringify;

final class CastFunctionsTest extends TestCase
{
    public function testToArrayReturnsArrayWhenValueIsArray(): void
    {
        $value = ['key' => 'value'];
        $result = arrayify($value);
        $this->assertEquals($value, $result);
    }

    public function testToArrayReturnsDefaultWhenValueIsNotArray(): void
    {
        $value = 'not an array';
        $default = ['default'];
        $result = arrayify($value, $default);
        $this->assertEquals($default, $result);
    }

    public function testToStringReturnsStringWhenValueIsString(): void
    {
        $value = 'string';
        $result = stringify($value);
        $this->assertEquals($value, $result);
    }

    public function testToStringReturnsDefaultWhenValueIsNotString(): void
    {
        $value = new stdClass();
        $default = 'default';
        $result = stringify($value, $default);
        $this->assertEquals($default, $result);
    }

    public function testToIntReturnsIntWhenValueIsInt(): void
    {
        $value = 123;
        $result = integerify($value);
        $this->assertEquals($value, $result);
    }

    public function testToIntReturnsDefaultWhenValueIsNotInt(): void
    {
        $value = 'not an int';
        $default = 456;
        $result = integerify($value, $default);
        $this->assertEquals($default, $result);
    }

    public function testToBoolReturnsBoolWhenValueIsBool(): void
    {
        $result = boolify(true);
        $this->assertTrue($result);
    }

    public function testToBoolReturnsDefaultWhenValueIsNotBool(): void
    {
        $value = 'not a bool';
        $result = boolify($value, true);
        $this->assertTrue($result);
    }

    public function testBoolifyParsesStringPositives(): void
    {
        $this->assertTrue(boolify('1'));
        $this->assertTrue(boolify('true'));
        $this->assertTrue(boolify('TRUE'));
        $this->assertTrue(boolify('on'));
        $this->assertTrue(boolify('ON'));
        $this->assertTrue(boolify('yes'));
        $this->assertTrue(boolify('Y'));
        $this->assertTrue(boolify('yEs'));
    }

    public function testBoolifyParsesStringNegatives(): void
    {
        $this->assertFalse(boolify('0'));
        $this->assertFalse(boolify('false'));
        $this->assertFalse(boolify('FALSE'));
        $this->assertFalse(boolify('off'));
        $this->assertFalse(boolify('OFF'));
        $this->assertFalse(boolify('no'));
        $this->assertFalse(boolify('n'));
        $this->assertFalse(boolify(''));
    }

    public function testBoolifyHandlesNumericValues(): void
    {
        $this->assertTrue(boolify(1));
        $this->assertFalse(boolify(0));
        $this->assertTrue(boolify(10));
        $this->assertTrue(boolify(-3));
        $this->assertTrue(boolify('2'));
        $this->assertFalse(boolify('0'));
    }

    public function testFloatifyReturnsFloatWhenValueIsFloat(): void
    {
        $value = 123.45;
        $result = floatify($value);
        $this->assertEquals($value, $result);
    }

    public function testFloatifyReturnsDefaultWhenValueIsNotFloat(): void
    {
        $value = 'not a float';
        $default = 456.78;
        $result = floatify($value, $default);
        $this->assertEquals($default, $result);
    }

    public function testFloatifyConvertsNumericStringsToFloat(): void
    {
        $value = '123.45';
        $result = floatify($value);
        $this->assertEquals(123.45, $result);
    }

    public function testFloatifyConvertsIntegersToFloat(): void
    {
        $value = 123;
        $result = floatify($value);
        $this->assertEquals(123.0, $result);
        $this->assertIsFloat($result);
    }

    public function testMapifyConvertsObjectToArray(): void
    {
        $object = new stdClass();
        $object->name = 'John';
        $object->age = 30;
        $result = mapify($object);
        $this->assertEquals(
            [
                'name' => 'John',
                'age' => 30,
            ],
            $result
        );
    }

    public function testMapifyReturnsArrayWhenValueIsArray(): void
    {
        $array = [
            'key' => 'value',
            'number' => 42,
        ];
        $result = mapify($array);
        $this->assertEquals($array, $result);
    }

    public function testMapifyHandlesNumericKeys(): void
    {
        $array = [
            0 => 'first',
            1 => 'second',
            'name' => 'test',
        ];
        $result = mapify($array);
        $expected = [
            'key_0' => 'first',
            'key_1' => 'second',
            'name' => 'test',
        ];
        $this->assertEquals($expected, $result);
    }

    public function testMapifyReturnsDefaultWhenValueIsNotArrayOrObject(): void
    {
        $value = 'not an array or object';
        $default = ['default' => 'value'];
        $result = mapify($value, $default);
        $this->assertEquals($default, $result);
    }

    public function testMapifyReturnsEmptyArrayByDefault(): void
    {
        $value = 'not an array or object';
        $result = mapify($value);
        $this->assertEquals([], $result);
    }

    public function testStringifyWithStringableObject(): void
    {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'stringable object';
            }
        };
        $result = stringify($stringable);
        $this->assertEquals('stringable object', $result);
    }

    public function testStringifyWithToStringMethod(): void
    {
        $object = new class {
            public function __toString(): string
            {
                return 'object with toString';
            }
        };
        $result = stringify($object);
        $this->assertEquals('object with toString', $result);
    }

    public function testStringifyWithScalarValues(): void
    {
        $this->assertEquals('123', stringify(123));
        $this->assertEquals('123.45', stringify(123.45));
        $this->assertEquals('1', stringify(true));
        $this->assertEquals('', stringify(false));
    }

    public function testIntegerifyWithNumericString(): void
    {
        $this->assertEquals(123, integerify('123'));
        $this->assertEquals(123, integerify('123.45'));
        $this->assertEquals(0, integerify('not numeric'));
    }
}
