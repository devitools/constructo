<?php

declare(strict_types=1);

namespace Constructo\Test\Support;

use Constructo\Contract\Exportable;
use Constructo\Support\Entity;
use JsonSerializable;
use PHPUnit\Framework\TestCase;

final class EntityTest extends TestCase
{
    public function testExportReturnsObjectWithAllProperties(): void
    {
        $entity = new class extends Entity {
            public string $name = 'John';
            public int $age = 30;
            public bool $active = true;
        };

        $result = $entity->export();

        $this->assertIsObject($result);
        $this->assertEquals('John', $result->name);
        $this->assertEquals(30, $result->age);
        $this->assertTrue($result->active);
    }

    public function testExportWithPublicPropertiesOnly(): void
    {
        $entity = new class extends Entity {
            public string $public = 'public';
            public int $number = 42;
            public bool $flag = true;
        };

        $result = $entity->export();

        $this->assertIsObject($result);
        $this->assertEquals('public', $result->public);
        $this->assertEquals(42, $result->number);
        $this->assertTrue($result->flag);
    }

    public function testExportWithEmptyEntity(): void
    {
        $entity = new class extends Entity {
        };

        $result = $entity->export();

        $this->assertIsObject($result);
        $this->assertEquals(new \stdClass(), $result);
    }

    public function testExportWithNullValues(): void
    {
        $entity = new class extends Entity {
            public ?string $nullable = null;
            public string $notNull = 'value';
        };

        $result = $entity->export();

        $this->assertIsObject($result);
        $this->assertNull($result->nullable);
        $this->assertEquals('value', $result->notNull);
    }

    public function testExportWithArrayProperty(): void
    {
        $entity = new class extends Entity {
            public array $items = ['a', 'b', 'c'];
            public array $assoc = ['key' => 'value'];
        };

        $result = $entity->export();

        $this->assertIsObject($result);
        $this->assertEquals(['a', 'b', 'c'], $result->items);
        $this->assertEquals(['key' => 'value'], $result->assoc);
    }

    public function testExportWithObjectProperty(): void
    {
        $entity = new class extends Entity {
            public \stdClass $object;

            public function __construct()
            {
                $this->object = new \stdClass();
                $this->object->prop = 'value';
            }
        };

        $result = $entity->export();

        $this->assertIsObject($result);
        $this->assertInstanceOf(\stdClass::class, $result->object);
        $this->assertEquals('value', $result->object->prop);
    }

    public function testJsonSerializeCallsExport(): void
    {
        $entity = new class extends Entity {
            public string $name = 'Test';
        };

        $jsonResult = $entity->jsonSerialize();
        $exportResult = $entity->export();

        $this->assertEquals($exportResult, $jsonResult);
    }

    public function testJsonSerializeReturnsObject(): void
    {
        $entity = new class extends Entity {
            public string $name = 'Test';
            public int $value = 42;
        };

        $result = $entity->jsonSerialize();

        $this->assertIsObject($result);
        $this->assertEquals('Test', $result->name);
        $this->assertEquals(42, $result->value);
    }

    public function testEntityImplementsExportable(): void
    {
        $entity = new class extends Entity {
        };

        $this->assertInstanceOf(Exportable::class, $entity);
    }

    public function testEntityImplementsJsonSerializable(): void
    {
        $entity = new class extends Entity {
        };

        $this->assertInstanceOf(JsonSerializable::class, $entity);
    }

    public function testEntityCanBeJsonEncoded(): void
    {
        $entity = new class extends Entity {
            public string $name = 'Test';
            public int $value = 42;
        };

        $json = json_encode($entity);

        $this->assertIsString($json);
        $this->assertStringContainsString('"name":"Test"', $json);
        $this->assertStringContainsString('"value":42', $json);
    }

    public function testExportWithDynamicProperties(): void
    {
        $entity = new class extends Entity {
            public string $dynamic = 'added at runtime';
        };

        $result = $entity->export();

        $this->assertIsObject($result);
        $this->assertEquals('added at runtime', $result->dynamic);
    }

    public function testExportWithMixedPropertyTypes(): void
    {
        $entity = new class extends Entity {
            public string $string = 'text';
            public int $integer = 123;
            public float $float = 45.67;
            public bool $boolean = false;
            public array $array = [1, 2, 3];
            public ?string $null = null;
        };

        $result = $entity->export();

        $this->assertIsObject($result);
        $this->assertEquals('text', $result->string);
        $this->assertEquals(123, $result->integer);
        $this->assertEquals(45.67, $result->float);
        $this->assertFalse($result->boolean);
        $this->assertEquals([1, 2, 3], $result->array);
        $this->assertNull($result->null);
    }
}
