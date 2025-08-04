<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Metadata\Schema\Field\Formatter;

use BadMethodCallException;
use Constructo\Support\Metadata\Schema\Field\Formatter\PatternFormatter;
use PHPUnit\Framework\TestCase;

final class PatternFormatterTest extends TestCase
{
    private PatternFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new PatternFormatter();
    }

    public function testFormatWithSingleElement(): void
    {
        $value = ['Hello World'];
        $result = $this->formatter->format($value);

        $this->assertSame(['Hello World'], $result);
    }

    public function testFormatWithSingleElementNumber(): void
    {
        $value = [123];
        $result = $this->formatter->format($value);

        $this->assertSame(['123'], $result);
    }

    public function testFormatWithSingleElementBoolean(): void
    {
        $value = [true];
        $result = $this->formatter->format($value);

        $this->assertSame(['1'], $result);
    }

    public function testFormatWithSingleElementNull(): void
    {
        $value = [null];
        $result = $this->formatter->format($value);

        $this->assertSame([''], $result);
    }

    public function testFormatWithPatternAndParameters(): void
    {
        $value = ['Hello %s, you are %d years old', ['John', 25]];
        $result = $this->formatter->format($value);

        $this->assertSame(['Hello John, you are 25 years old'], $result);
    }

    public function testFormatWithPatternAndSingleParameter(): void
    {
        $value = ['Welcome %s!', ['Alice']];
        $result = $this->formatter->format($value);

        $this->assertSame(['Welcome Alice!'], $result);
    }

    public function testFormatWithPatternAndNullParameters(): void
    {
        $value = ['Simple pattern', null];
        $result = $this->formatter->format($value);

        $this->assertSame(['Simple pattern'], $result);
    }

    public function testFormatWithPatternAndEmptyParameters(): void
    {
        $value = ['No parameters', []];
        $result = $this->formatter->format($value);

        $this->assertSame(['No parameters'], $result);
    }

    public function testFormatWithPatternAndClosureParameters(): void
    {
        $closure = fn() => ['Dynamic', 'Parameters'];
        $value = ['%s %s from closure', $closure];
        $result = $this->formatter->format($value);

        $this->assertSame(['Dynamic Parameters from closure'], $result);
    }

    public function testFormatWithPatternAndClosureReturningString(): void
    {
        $closure = fn() => ['SingleValue'];
        $value = ['Value: %s', $closure];
        $result = $this->formatter->format($value);

        $this->assertSame(['Value: SingleValue'], $result);
    }

    public function testFormatWithComplexPattern(): void
    {
        $value = ['User: %s (ID: %d, Active: %s)', ['John Doe', 123, 'Yes']];
        $result = $this->formatter->format($value);

        $this->assertSame(['User: John Doe (ID: 123, Active: Yes)'], $result);
    }

    public function testFormatWithNumericPattern(): void
    {
        $value = ['%.2f%%', [85.678]];
        $result = $this->formatter->format($value);

        $this->assertSame(['85.68%'], $result);
    }

    public function testFormatWithNonArrayThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('PatternFormatter expects an array value.');

        $this->formatter->format('not an array');
    }

    public function testFormatWithNullThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('PatternFormatter expects an array value.');

        $this->formatter->format(null);
    }

    public function testFormatWithEmptyArrayThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('PatternFormatter expects an array with one or two elements.');

        $this->formatter->format([]);
    }

    public function testFormatWithThreeElementsThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('PatternFormatter expects an array with one or two elements.');

        $this->formatter->format(['pattern', ['param1'], 'extra']);
    }

    public function testFormatWithFourElementsThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('PatternFormatter expects an array with one or two elements.');

        $this->formatter->format(['pattern', ['param1'], 'extra', 'more']);
    }

    public function testFormatWithParametersMethod(): void
    {
        $value = ['Hello %s!', ['World']];
        $result = $this->formatter->formatWithParameters($value);

        $this->assertSame(['Hello World!'], $result);
    }

    public function testFormatWithParametersMethodAndClosure(): void
    {
        $closure = fn() => ['Closure', 'Test'];
        $value = ['%s %s', $closure];
        $result = $this->formatter->formatWithParameters($value);

        $this->assertSame(['Closure Test'], $result);
    }

    public function testFormatWithOption(): void
    {
        $value = ['Test %s', ['value']];
        $result = $this->formatter->format($value, 'some_option');

        $this->assertSame(['Test value'], $result);
    }
}
