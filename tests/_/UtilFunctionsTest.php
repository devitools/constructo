<?php

declare(strict_types=1);

namespace Constructo\Test\_;

use PHPUnit\Framework\TestCase;

use function Constructo\Util\extractArray;
use function Constructo\Util\extractString;
use function Constructo\Util\extractInt;
use function Constructo\Util\extractBool;
use function Constructo\Util\extractNumeric;

final class UtilFunctionsTest extends TestCase
{
    public function testExtractArrayReturnsArrayWhenPropertyExists(): void
    {
        $array = ['items' => ['a', 'b', 'c']];
        $result = extractArray($array, 'items');
        $this->assertEquals(['a', 'b', 'c'], $result);
    }

    public function testExtractArrayReturnsDefaultWhenPropertyDoesNotExist(): void
    {
        $array = ['other' => 'value'];
        $default = ['default', 'array'];
        $result = extractArray($array, 'missing', $default);
        $this->assertEquals($default, $result);
    }

    public function testExtractArrayReturnsDefaultWhenPropertyIsNotArray(): void
    {
        $array = ['items' => 'not an array'];
        $default = ['default'];
        $result = extractArray($array, 'items', $default);
        $this->assertEquals($default, $result);
    }

    public function testExtractArrayReturnsEmptyArrayByDefault(): void
    {
        $array = ['other' => 'value'];
        $result = extractArray($array, 'missing');
        $this->assertEquals([], $result);
    }

    public function testExtractStringReturnsStringWhenPropertyExists(): void
    {
        $array = ['name' => 'John Doe'];
        $result = extractString($array, 'name');
        $this->assertEquals('John Doe', $result);
    }

    public function testExtractStringReturnsDefaultWhenPropertyDoesNotExist(): void
    {
        $array = ['other' => 'value'];
        $default = 'default string';
        $result = extractString($array, 'missing', $default);
        $this->assertEquals($default, $result);
    }

    public function testExtractStringReturnsDefaultWhenPropertyIsNotString(): void
    {
        $array = ['name' => 123];
        $default = 'default';
        $result = extractString($array, 'name', $default);
        $this->assertEquals($default, $result);
    }

    public function testExtractStringReturnsEmptyStringByDefault(): void
    {
        $array = ['other' => 'value'];
        $result = extractString($array, 'missing');
        $this->assertEquals('', $result);
    }

    public function testExtractIntReturnsIntWhenPropertyExists(): void
    {
        $array = ['count' => 42];
        $result = extractInt($array, 'count');
        $this->assertEquals(42, $result);
    }

    public function testExtractIntReturnsDefaultWhenPropertyDoesNotExist(): void
    {
        $array = ['other' => 'value'];
        $default = 100;
        $result = extractInt($array, 'missing', $default);
        $this->assertEquals($default, $result);
    }

    public function testExtractIntReturnsDefaultWhenPropertyIsNotInt(): void
    {
        $array = ['count' => 'not an int'];
        $default = 50;
        $result = extractInt($array, 'count', $default);
        $this->assertEquals($default, $result);
    }

    public function testExtractIntReturnsZeroByDefault(): void
    {
        $array = ['other' => 'value'];
        $result = extractInt($array, 'missing');
        $this->assertEquals(0, $result);
    }

    public function testExtractBoolReturnsBoolWhenPropertyExists(): void
    {
        $array = ['active' => true];
        $result = extractBool($array, 'active');
        $this->assertTrue($result);

        $array = ['active' => false];
        $result = extractBool($array, 'active');
        $this->assertFalse($result);
    }

    public function testExtractBoolReturnsDefaultWhenPropertyDoesNotExist(): void
    {
        $array = ['other' => 'value'];
        $default = true;
        $result = extractBool($array, 'missing', $default);
        $this->assertTrue($result);
    }

    public function testExtractBoolReturnsDefaultWhenPropertyIsNotBool(): void
    {
        $array = ['active' => 'not a bool'];
        $default = true;
        $result = extractBool($array, 'active', $default);
        $this->assertTrue($result);
    }

    public function testExtractBoolReturnsFalseByDefault(): void
    {
        $array = ['other' => 'value'];
        $result = extractBool($array, 'missing');
        $this->assertFalse($result);
    }

    public function testExtractNumericReturnsFloatWhenPropertyIsNumeric(): void
    {
        $array = ['price' => 19.99];
        $result = extractNumeric($array, 'price');
        $this->assertEquals(19.99, $result);

        $array = ['count' => 42];
        $result = extractNumeric($array, 'count');
        $this->assertEquals(42.0, $result);

        $array = ['value' => '123.45'];
        $result = extractNumeric($array, 'value');
        $this->assertEquals(123.45, $result);
    }

    public function testExtractNumericReturnsDefaultWhenPropertyDoesNotExist(): void
    {
        $array = ['other' => 'value'];
        $default = 99.9;
        $result = extractNumeric($array, 'missing', $default);
        $this->assertEquals($default, $result);
    }

    public function testExtractNumericReturnsDefaultWhenPropertyIsNotNumeric(): void
    {
        $array = ['price' => 'not numeric'];
        $default = 10.5;
        $result = extractNumeric($array, 'price', $default);
        $this->assertEquals($default, $result);
    }

    public function testExtractNumericReturnsZeroByDefault(): void
    {
        $array = ['other' => 'value'];
        $result = extractNumeric($array, 'missing');
        $this->assertEquals(0.0, $result);
    }

    public function testExtractNumericWithIntegerDefault(): void
    {
        $array = ['other' => 'value'];
        $result = extractNumeric($array, 'missing', 5);
        $this->assertEquals(5.0, $result);
    }
}
