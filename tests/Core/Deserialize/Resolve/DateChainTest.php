<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Deserialize\Resolve;

use Constructo\Core\Deserialize\Resolve\DateChain;
use Constructo\Type\Timestamp;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

final class DateChainTest extends TestCase
{
    public function testResolveWithTimestamp(): void
    {
        // Arrange
        $chain = new DateChain();
        $parameter = $this->createMock(ReflectionParameter::class);
        $timestamp = new Timestamp();

        // Act
        $result = $chain->resolve($parameter, $timestamp);

        // Assert
        $this->assertEquals($timestamp->toString(), $result->content);
    }

    public function testResolveWithDateTimeInterface(): void
    {
        // Arrange
        $chain = new DateChain();
        $parameter = $this->createMock(ReflectionParameter::class);
        $dateTime = new DateTimeImmutable();

        // Act
        $result = $chain->resolve($parameter, $dateTime);

        // Assert
        $this->assertEquals($dateTime->format(DateTimeInterface::ATOM), $result->content);
    }

    public function testResolveWithNonDateValue(): void
    {
        // Arrange
        $value = 'not a date';
        $parameter = $this->createMock(ReflectionParameter::class);

        // Create a test double of DateChain that extends the real DateChain
        $chain = new DateChain();

        // Act
        $result = $chain->resolve($parameter, $value);

        // Assert
        $this->assertEquals($value, $result->content);
    }
}
