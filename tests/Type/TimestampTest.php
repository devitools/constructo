<?php

declare(strict_types=1);

namespace Constructo\Test\Type;

use Constructo\Type\Timestamp;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

final class TimestampTest extends TestCase
{
    public function testJsonSerializeReturnsAtomFormat(): void
    {
        $timestamp = new Timestamp('2023-01-15 10:30:45');

        $result = $timestamp->jsonSerialize();

        $this->assertIsString($result);
        $this->assertStringContainsString('2023-01-15T10:30:45', $result);
        $this->assertStringContainsString('+', $result);
        $this->assertSame($result, (string) $timestamp);
    }

    public function testToStringReturnsAtomFormat(): void
    {
        $timestamp = new Timestamp('2023-01-15 10:30:45');

        $result = $timestamp->toString();

        $this->assertIsString($result);
        $this->assertStringContainsString('2023-01-15T10:30:45', $result);
        $this->assertStringContainsString('+', $result);
    }

    public function testJsonSerializeAndToStringReturnSameValue(): void
    {
        $timestamp = new Timestamp('2023-01-15 10:30:45');

        $jsonResult = $timestamp->jsonSerialize();
        $toStringResult = $timestamp->toString();

        $this->assertSame($jsonResult, $toStringResult);
    }

    public function testTimestampWithDifferentTimezone(): void
    {
        $timestamp = new Timestamp('2023-01-15 10:30:45', new \DateTimeZone('UTC'));

        $result = $timestamp->toString();

        $this->assertStringContainsString('2023-01-15T10:30:45+00:00', $result);
    }

    public function testTimestampWithMicroseconds(): void
    {
        $timestamp = new Timestamp('2023-01-15 10:30:45.123456');

        $result = $timestamp->toString();

        $this->assertStringContainsString('2023-01-15T10:30:45', $result);
        $this->assertIsString($result);
    }

    public function testTimestampInheritsFromDateTimeImmutable(): void
    {
        $timestamp = new Timestamp('2023-01-15 10:30:45');

        $this->assertInstanceOf(\DateTimeImmutable::class, $timestamp);
        $this->assertInstanceOf(DateTimeInterface::class, $timestamp);
    }

    public function testTimestampImplementsJsonSerializable(): void
    {
        $timestamp = new Timestamp('2023-01-15 10:30:45');

        $this->assertInstanceOf(\JsonSerializable::class, $timestamp);
    }

    public function testTimestampCanBeJsonEncoded(): void
    {
        $timestamp = new Timestamp('2023-01-15 10:30:45');

        $json = json_encode($timestamp);

        $this->assertIsString($json);
        $this->assertStringContainsString('2023-01-15T10:30:45', $json);
    }

    public function testTimestampWithCurrentTime(): void
    {
        $timestamp = new Timestamp();

        $result = $timestamp->toString();

        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}/', $result);
    }

    public function testTimestampFromUnixTimestamp(): void
    {
        $unixTime = 1673776245; // 2023-01-15 09:50:45 UTC
        $timestamp = new Timestamp('@' . $unixTime);

        $result = $timestamp->toString();

        $this->assertStringContainsString('2023-01-15T09:50:45+00:00', $result);
    }
}
