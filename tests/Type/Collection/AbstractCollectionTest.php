<?php

declare(strict_types=1);

namespace Constructo\Test\Type\Collection;

use Constructo\Type\Collection\AbstractCollection;
use PHPUnit\Framework\TestCase;

final class AbstractCollectionTest extends TestCase
{
    public function testShouldRewind(): void
    {
        $collection = new class ([1, 2, 3]) extends AbstractCollection {
            public function current(): mixed
            {
                return $this->datum();
            }
        };

        $collection->next();
        $collection->rewind();

        $this->assertEquals(0, $collection->key());
        $this->assertEquals(1, $collection->current());
    }

    public function testShouldReturnKey(): void
    {
        $collection = new class ([1, 2, 3]) extends AbstractCollection {
            public function current(): mixed
            {
                return $this->datum();
            }
        };

        $collection->next();

        $this->assertEquals(1, $collection->key());
    }

    public function testShouldReturnNext(): void
    {
        $collection = new class ([1, 2, 3]) extends AbstractCollection {
            public function current(): mixed
            {
                return $this->datum();
            }
        };

        $collection->next();

        $this->assertEquals(1, $collection->key());
    }

    public function testShouldReturnValid(): void
    {
        $collection = new class ([1, 2, 3]) extends AbstractCollection {
            public function current(): mixed
            {
                return $this->datum();
            }
        };

        $collection->next();

        $this->assertTrue($collection->valid());
    }

    public function testShouldReturnCount(): void
    {
        $collection = new class ([1, 2, 3]) extends AbstractCollection {
            public function current(): mixed
            {
                return $this->datum();
            }
        };

        $this->assertEquals(3, $collection->count());
    }

    public function testShouldReturnRows(): void
    {
        $collection = new class ([1, 2, 3]) extends AbstractCollection {
            public function current(): mixed
            {
                return $this->datum();
            }

            public function length(): int
            {
                return count($this->data());
            }
        };

        $this->assertEquals(3, $collection->length());
    }

    public function testShouldReturnAll(): void
    {
        $collection = new class ([1, 2, 3]) extends AbstractCollection {
            public function current(): mixed
            {
                return $this->datum();
            }
        };

        $this->assertEquals([1, 2, 3], $collection->all());
    }

    public function testShouldReturnMap(): void
    {
        $collection = new class ([1, 2, 3]) extends AbstractCollection {
            public function current(): mixed
            {
                return $this->datum();
            }
        };

        $result = $collection->map(fn ($item) => $item * 2);

        $this->assertEquals([2, 4, 6], $result);
    }
}
