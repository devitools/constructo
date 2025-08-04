<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Metadata\Schema\Field\Formatter;

use BackedEnum;
use BadMethodCallException;
use Constructo\Core\Metadata\Schema\Field\Formatter\MergeFormatter;
use PHPUnit\Framework\TestCase;

enum TestStringEnum: string
{
    case FIRST = 'first';
    case SECOND = 'second';
    case THIRD = 'third';
}

enum TestIntEnum: int
{
    case ONE = 1;
    case TWO = 2;
    case THREE = 3;
}

final class MergeFormatterTest extends TestCase
{
    private MergeFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new MergeFormatter();
    }

    public function testFormatWithStringArray(): void
    {
        $value = [['apple', 'banana', 'cherry']];
        $result = $this->formatter->format($value);

        $this->assertSame(['apple', 'banana', 'cherry'], $result);
    }

    public function testFormatWithMixedArray(): void
    {
        $value = [['apple', 123, true, null]];
        $result = $this->formatter->format($value);

        $this->assertSame(['apple', '123', '1', ''], $result);
    }

    public function testFormatWithBackedEnumInstances(): void
    {
        $value = [[TestStringEnum::FIRST, TestStringEnum::SECOND, TestStringEnum::THIRD]];
        $result = $this->formatter->format($value);

        $this->assertSame(['first', 'second', 'third'], $result);
    }

    public function testFormatWithIntBackedEnumInstances(): void
    {
        $value = [[TestIntEnum::ONE, TestIntEnum::TWO, TestIntEnum::THREE]];
        $result = $this->formatter->format($value);

        $this->assertSame([1, 2, 3], $result);
    }

    public function testFormatWithBackedEnumClass(): void
    {
        $value = [TestStringEnum::class];
        $result = $this->formatter->format($value);

        $this->assertSame(['first', 'second', 'third'], $result);
    }

    public function testFormatWithIntBackedEnumClass(): void
    {
        $value = [TestIntEnum::class];
        $result = $this->formatter->format($value);

        $this->assertSame([1, 2, 3], $result);
    }

    public function testFormatWithSingleItem(): void
    {
        $value = [['single']];
        $result = $this->formatter->format($value);

        $this->assertSame(['single'], $result);
    }

    public function testFormatWithEmptyArrayThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('MergeFormatter requires an array with at least one element.');

        $this->formatter->format([]);
    }

    public function testFormatWithNonArrayThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('MergeFormatter requires an array with at least one element.');

        $this->formatter->format('not an array');
    }

    public function testFormatWithNullThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('MergeFormatter requires an array with at least one element.');

        $this->formatter->format(null);
    }

    public function testFormatWithEmptyNestedArray(): void
    {
        $value = [[]];
        $result = $this->formatter->format($value);

        $this->assertSame([], $result);
    }

    public function testFormatWithMixedEnumAndStringArray(): void
    {
        $value = [[TestStringEnum::FIRST, 'regular_string', TestStringEnum::SECOND]];
        $result = $this->formatter->format($value);

        $this->assertSame(['first', 'regular_string', 'second'], $result);
    }

    public function testFormatWithOption(): void
    {
        $value = [['test']];
        $result = $this->formatter->format($value, 'some_option');

        $this->assertSame(['test'], $result);
    }
}
