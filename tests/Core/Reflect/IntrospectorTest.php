<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Reflect;

use Constructo\Core\Reflect\Introspection\Introspector;
use Constructo\Test\Stub\Domain\Collection\Game\FeatureCollection;
use Constructo\Test\Stub\Domain\Entity\Game\Feature;
use Iterator;
use PHPUnit\Framework\TestCase;
use stdClass;

final class IntrospectorTest extends TestCase
{
    private Introspector $introspector;

    protected function setUp(): void
    {
        $this->introspector = new Introspector();
    }

    public function testAnalyzeReturnsResultForNonIteratorClass(): void
    {
        $result = $this->introspector->analyze(stdClass::class);

        $this->assertEquals(stdClass::class, $result->source);
        $this->assertNull($result->introspectable());
    }

    public function testAnalyzeReturnsResultForNonExistentClass(): void
    {
        $result = $this->introspector->analyze('NonExistentClass');

        $this->assertEquals('NonExistentClass', $result->source);
        $this->assertNull($result->introspectable());
    }

    public function testAnalyzeReturnsCorrectTypeForIteratorWithTypedCurrentMethod(): void
    {
        $result = $this->introspector->analyze(FeatureCollection::class);

        $this->assertEquals(FeatureCollection::class, $result->source);
        $this->assertEquals(Feature::class, $result->introspectable());
    }

    public function testAnalyzeReturnsNullForIteratorWithMixedReturnType(): void
    {
        $iterator = new class implements Iterator {
            public function current(): mixed {
                return null;
            }

            public function next(): void {}
            public function key(): mixed { return null; }
            public function valid(): bool { return false; }
            public function rewind(): void {}
        };

        $result = $this->introspector->analyze($iterator::class);

        $this->assertEquals($iterator::class, $result->source);
        $this->assertNull($result->introspectable());
    }

    public function testAnalyzeHandlesBuiltInTypes(): void
    {
        $iterator = new class implements Iterator {
            public function current(): string {
                return '';
            }

            public function next(): void {}
            public function key(): mixed { return null; }
            public function valid(): bool { return false; }
            public function rewind(): void {}
        };

        $result = $this->introspector->analyze($iterator::class);

        $this->assertEquals($iterator::class, $result->source);
        $this->assertNull($result->introspectable());
    }

    public function testAnalyzeHandlesUnionTypes(): void
    {
        $iterator = new class implements Iterator {
            public function current(): string|int {
                return '';
            }

            public function next(): void {}
            public function key(): mixed { return null; }
            public function valid(): bool { return false; }
            public function rewind(): void {}
        };

        $result = $this->introspector->analyze($iterator::class);

        $this->assertEquals($iterator::class, $result->source);
        $this->assertNull($result->introspectable());
    }
}
