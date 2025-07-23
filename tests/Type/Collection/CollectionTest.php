<?php

declare(strict_types=1);

namespace Constructo\Test\Type\Collection;

use Constructo\Support\Datum;
use Constructo\Test\Type\Collection\CollectionTestMock as Collection;
use Constructo\Test\Type\Collection\CollectionTestMockStub as Stub;
use DomainException;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

final class CollectionTest extends TestCase
{
    public function testShouldCreateFromArray(): void
    {
        $collection = new Collection();
        $collection->push(new Stub('foo'));
        $collection->push(new Stub('bar'));

        $this->assertCount(2, $collection);
    }

    public function testShouldFailOnInvalidDatum(): void
    {
        $this->expectException(DomainException::class);

        $collection = new Collection();
        $collection->push(new stdClass());
    }

    public function testShouldExportData(): void
    {
        $collection = new Collection();
        $stub1 = new Stub('foo');
        $stub2 = new Stub('bar');

        $collection->push($stub1);
        $collection->push($stub2);

        $exported = $collection->export();

        $this->assertIsArray($exported);
        $this->assertCount(2, $exported);
        $this->assertSame($stub1, $exported[0]);
        $this->assertSame($stub2, $exported[1]);
    }

    public function testShouldAllowNonStrictMode(): void
    {
        // Arrange
        $collection = (new Collection())->unsafe(true);
        $datum = new Datum(new Exception(), []);

        // Act
        $collection->push($datum);

        // Assert
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(Datum::class, $collection->all()[0]);
    }
}
