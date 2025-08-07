<?php

declare(strict_types=1);

namespace Constructo\Test\_;

use PHPUnit\Framework\TestCase;

final class FunctionsPolyfillTest extends TestCase
{
    public function testArrayFlattenShouldFlattenNestedArrays(): void
    {
        $array = [
            'a' => 1,
            'b' => [
                'c' => 2,
                'd' => 3,
            ],
            'e' => [
                'f' => [
                    'g' => 4,
                ],
            ],
        ];

        $expected = [
            'a' => 1,
            'c' => 2,
            'd' => 3,
            'g' => 4,
        ];

        $this->assertEquals($expected, array_flatten($array));
    }

    public function testArrayFlattenPrefixedShouldFlattenWithPrefixes(): void
    {
        $array = [
            'a' => 1,
            'b' => [
                'c' => 2,
                'd' => 3,
            ],
            'e' => [
                'f' => [
                    'g' => 4,
                ],
            ],
        ];

        $expected = [
            'a' => 1,
            'b.c' => 2,
            'b.d' => 3,
            'e.f.g' => 4,
        ];

        $this->assertEquals($expected, array_flatten_prefixed($array));
    }

    public function testArrayShiftPluckInt(): void
    {
        $array = [
            [
                'id' => 1,
                'name' => 'test',
            ],
            [
                'id' => 2,
                'name' => 'test2',
            ],
        ];

        $this->assertEquals(1, array_shift_pluck_int($array, 'id'));
    }

    public function testArrayShiftPluckIntReturnsNullForEmptyArray(): void
    {
        $this->assertNull(array_shift_pluck_int([], 'id'));
    }

    public function testArrayShiftPluckIntReturnsNullForNonNumericValue(): void
    {
        $array = [['id' => 'abc']];
        $this->assertNull(array_shift_pluck_int($array, 'id'));
    }

    public function testArrayFirst(): void
    {
        $array = [
            'a',
            'b',
            'c',
        ];
        $this->assertEquals('a', array_first($array));
    }

    public function testArrayFirstReturnsNullForEmptyArray(): void
    {
        $this->assertNull(array_first([]));
    }

    public function testArrayUnshiftKey(): void
    {
        $array = [
            'a' => [
                1,
                2,
            ],
        ];
        $result = array_unshift_key($array, 'a', 3);
        $this->assertEquals(
            [
                'a' => [
                    1,
                    2,
                    3,
                ],
            ],
            $result
        );

        $array = [];
        $result = array_unshift_key($array, 'a', 1);
        $this->assertEquals(['a' => [1]], $result);
    }

    public function testArrayExportWithSimpleArray(): void
    {
        $array = ['a', 'b', 'c'];
        $expected = "['a', 'b', 'c']";
        $this->assertEquals($expected, array_export($array));
    }

    public function testArrayExportWithAssociativeArray(): void
    {
        $array = ['name' => 'John', 'age' => 30];
        $expected = "['name' => 'John', 'age' => 30]";
        $this->assertEquals($expected, array_export($array));
    }

    public function testArrayExportWithMixedDataTypes(): void
    {
        $array = ['string' => 'text', 'int' => 42, 'bool' => true, 'null' => null];
        $expected = "['string' => 'text', 'int' => 42, 'bool' => 1, 'null' => null]";
        $this->assertEquals($expected, array_export($array));
    }

    public function testArrayExportWithObject(): void
    {
        $obj = new \stdClass();
        $obj->prop = 'value';
        $array = ['object' => $obj];
        $expected = "['object' => '{\"prop\":\"value\"}']";
        $this->assertEquals($expected, array_export($array));
    }

    public function testArrayExportWithNestedArrayCompact(): void
    {
        $array = ['a' => ['b' => 'c']];
        $expected = "['a' => ['b' => 'c']]";
        $this->assertEquals($expected, array_export($array));
    }

    public function testArrayExportWithLevelFormatting(): void
    {
        $array = ['a' => ['b' => 'c']];
        $expected = "[\n    'a' => ['b' => 'c'],\n]";
        $this->assertEquals($expected, array_export($array, 1));
    }

    public function testArrayExportWithDeepNesting(): void
    {
        $array = ['a' => ['b' => ['c' => 'd']]];
        $expected = "[\n    'a' => ['b' => ['c' => 'd']],\n]";
        $this->assertEquals($expected, array_export($array, 1));
    }

    public function testArrayExportWithMargin(): void
    {
        $array = ['a' => 'b'];
        $expected = "[\n      'a' => 'b',\n  ]";
        $this->assertEquals($expected, array_export($array, 1, 0, 2));
    }

    public function testArrayExportWithEmptyArray(): void
    {
        $array = [];
        $expected = "[]";
        $this->assertEquals($expected, array_export($array));
    }

    public function testArrayExportWithNumericKeys(): void
    {
        $array = [0 => 'zero', 1 => 'one', 5 => 'five'];
        $expected = "['zero', 'one', 'five']";
        $this->assertEquals($expected, array_export($array));
    }

    public function testArrayExportWithBooleanFalse(): void
    {
        $array = ['flag' => false];
        $expected = "['flag' => ]";
        $this->assertEquals($expected, array_export($array));
    }

    public function testArrayExportWithFloatValue(): void
    {
        $array = ['pi' => 3.14];
        $expected = "['pi' => 3.14]";
        $this->assertEquals($expected, array_export($array));
    }

    public function testArrayExportWithLevelZeroDeepNesting(): void
    {
        $array = ['a' => ['b' => ['c' => 'd']]];
        $expected = "['a' => ['b' => ['c' => 'd']]]";
        $this->assertEquals($expected, array_export($array, 0));
    }

    public function testArrayExportWithLevelLimitReached(): void
    {
        $array = ['a' => ['b' => ['c' => 'd']]];
        $expected = "[\n    'a' => [\n        'b' => ['c' => 'd'],\n    ],\n]";
        $this->assertEquals($expected, array_export($array, 2));
    }
}
