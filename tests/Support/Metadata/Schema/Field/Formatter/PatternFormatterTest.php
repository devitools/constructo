<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Metadata\Schema\Field\Formatter;

use BadMethodCallException;
use Constructo\Support\Metadata\Schema\Field\Formatter\PatternFormatter;
use PHPUnit\Framework\TestCase;

final class PatternFormatterTest extends TestCase
{
    public function testFormatWithNonArrayParameter(): void
    {
        $formatter = new PatternFormatter();
        $value = ['Hello %s!', 'World'];
        $result = $formatter->format($value);

        $this->assertSame(['Hello World!'], $result);
    }

    public function testFormatWithSingleElement(): void
    {
        $formatter = new PatternFormatter();
        $value = ['Hello World'];
        $result = $formatter->format($value);

        $this->assertSame(['Hello World'], $result);
    }

    public function testFormatWithSingleElementNumber(): void
    {
        $formatter = new PatternFormatter();
        $value = [123];
        $result = $formatter->format($value);

        $this->assertSame(['123'], $result);
    }

    public function testFormatWithPatternAndArrayParameters(): void
    {
        $formatter = new PatternFormatter();
        $value = ['Hello %s, you are %d years old', ['John', 25]];
        $result = $formatter->format($value);

        $this->assertSame(['Hello John, you are 25 years old'], $result);
    }

    public function testFormatWithPatternAndNullParameters(): void
    {
        $formatter = new PatternFormatter();
        $value = ['Simple pattern', null];
        $result = $formatter->format($value);

        $this->assertSame(['Simple pattern'], $result);
    }

    public function testFormatWithPatternAndClosureParameters(): void
    {
        $formatter = new PatternFormatter();
        $closure = fn() => ['Dynamic', 'Parameters'];
        $value = ['%s %s from closure', $closure];
        $result = $formatter->format($value);

        $this->assertSame(['Dynamic Parameters from closure'], $result);
    }

    public function testFormatWithNonArrayThrowsException(): void
    {
        $formatter = new PatternFormatter();

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('PatternFormatter expects an array value.');

        $formatter->format('not an array');
    }

    public function testFormatWithEmptyArrayThrowsException(): void
    {
        $formatter = new PatternFormatter();

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('PatternFormatter expects an array with one or two elements.');

        $formatter->format([]);
    }

    public function testFormatWithThreeElementsThrowsException(): void
    {
        $formatter = new PatternFormatter();

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('PatternFormatter expects an array with one or two elements.');

        $formatter->format(['pattern', ['param1'], 'extra']);
    }

    public function testNormalizeParametersWithNonArrayConvertsToArray(): void
    {
        $formatter = new PatternFormatter();
        $result = $formatter->normalizeParameters('single_value');

        $this->assertSame(['single_value'], $result);
    }

    public function testNormalizeParametersWithClosure(): void
    {
        $formatter = new PatternFormatter();
        $closure = fn() => ['test', 'values'];
        $result = $formatter->normalizeParameters($closure);

        $this->assertSame(['test', 'values'], $result);
    }

    public function testNormalizeParametersWithMixedTypes(): void
    {
        $formatter = new PatternFormatter();
        $parameters = [true, 42, 3.14, 'string', new \stdClass(), null];
        $result = $formatter->normalizeParameters($parameters);

        $this->assertSame([true, 42, 3.14, 'string', null, null], $result);
    }
}
