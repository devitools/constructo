<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Deserialize;

use Constructo\Contract\Exportable;
use Constructo\Core\Deserialize\Demolisher;
use Constructo\Type\Timestamp;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

final class DemolisherDateTest extends TestCase
{
    public function testShouldDemolishWithTimestamp(): void
    {
        // Arrange
        $demolisher = new Demolisher();
        $timestamp = new Timestamp();
        $instance = new readonly class($timestamp) implements Exportable {
            public function __construct(
                private Timestamp $createdAt
            ) {
            }

            public function export(): array
            {
                return [
                    'createdAt' => $this->createdAt,
                ];
            }
        };

        // Act
        $values = $demolisher->demolish($instance);

        // Assert
        $this->assertEquals($timestamp->toString(), $values->created_at);
    }

    public function testShouldDemolishWithDateTimeImmutable(): void
    {
        // Arrange
        $demolisher = new Demolisher();
        $dateTime = new DateTimeImmutable();
        $instance = new readonly class($dateTime) implements Exportable {
            public function __construct(
                private DateTimeImmutable $updatedAt
            ) {
            }

            public function export(): array
            {
                return [
                    'updatedAt' => $this->updatedAt,
                ];
            }
        };

        // Act
        $values = $demolisher->demolish($instance);

        // Assert
        $this->assertEquals($dateTime->format(DateTimeInterface::ATOM), $values->updated_at);
    }
}
