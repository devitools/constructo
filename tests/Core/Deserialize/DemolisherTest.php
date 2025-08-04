<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Deserialize;

use Constructo\Contract\Collectable;
use Constructo\Contract\Exportable;
use Constructo\Contract\Message;
use Constructo\Core\Deserialize\Demolisher;
use Constructo\Support\Datum;
use Constructo\Support\Set;
use Constructo\Test\Stub\Domain\Collection\Game\FeatureCollection;
use Constructo\Test\Stub\Domain\Entity\Command\GameCommand;
use Constructo\Test\Stub\Domain\Entity\Game\Feature;
use Constructo\Type\Timestamp;
use PHPUnit\Framework\TestCase;

final class DemolisherTest extends TestCase
{
    public function testShouldDemolish(): void
    {
        $demolisher = new Demolisher(formatters: [
            'string' => fn ($value) => sprintf('[%s]', $value),
        ]);
        $timestamp = new Timestamp();
        $instance = new GameCommand('Cool game', 'cool-game', $timestamp, [], new FeatureCollection());
        $values = $demolisher->demolish($instance);

        $this->assertEquals('[Cool game]', $values->name);
        $this->assertEquals('[cool-game]', $values->slug);
    }

    public function testShouldNotUseInvalidNovaValueParameter(): void
    {
        $demolisher = new Demolisher();
        $instance = new readonly class implements Exportable {
            public function __construct(public string $name = 'Jhon Doe')
            {
            }

            public function export(): array
            {
                return ['title' => $this->name];
            }
        };
        $values = $demolisher->demolish($instance);
        $this->assertEmpty(get_object_vars($values));
    }

    public function testShouldDemolishDatumInstance(): void
    {
        $demolisher = new Demolisher();
        $data = ['name' => 'Test', 'value' => 123];
        $exception = new \Exception('Test error');
        $datum = new Datum($exception, $data);

        $result = $demolisher->demolish($datum);

        $this->assertObjectHasProperty('name', $result);
        $this->assertObjectHasProperty('value', $result);
        $this->assertObjectHasProperty('@error', $result);
        $this->assertEquals('Test', $result->name);
        $this->assertEquals(123, $result->value);
    }

    public function testShouldDemolishEmptyParametersClass(): void
    {
        $demolisher = new Demolisher();
        $instance = new readonly class {
        };

        $result = $demolisher->demolish($instance);

        $this->assertEquals((object) [], $result);
    }

    public function testShouldDemolishCollection(): void
    {
        $demolisher = new Demolisher();
        $feature1 = new Feature('Feature 1', 'Description 1', true);
        $feature2 = new Feature('Feature 2', 'Description 2', false);
        $collection = new FeatureCollection();
        $collection->push($feature1);
        $collection->push($feature2);

        $result = $demolisher->demolishCollection($collection);

        $this->assertCount(2, $result);
        $this->assertEquals('Feature 1', $result[0]->name);
        $this->assertEquals('Feature 2', $result[1]->name);
    }

    public function testShouldDemolishCollectionWithNonExportableItems(): void
    {
        $demolisher = new Demolisher();
        $item1 = new readonly class('Item 1') {
            public function __construct(public string $name)
            {
            }
        };
        $item2 = new readonly class('Item 2') {
            public function __construct(public string $name)
            {
            }
        };

        $collection = new class([$item1, $item2]) implements Collectable {
            public function __construct(private array $items)
            {
            }

            public function all(): array
            {
                return $this->items;
            }

            public function push(object $datum): void
            {
                $this->items[] = $datum;
            }

            public function map(\Closure $param): mixed
            {
                return array_map($param, $this->items);
            }
        };

        $result = $demolisher->demolishCollection($collection);

        $this->assertCount(2, $result);
        $this->assertEquals($item1, $result[0]);
        $this->assertEquals($item2, $result[1]);
    }

    public function testShouldExtractValuesFromMessage(): void
    {
        $demolisher = new Demolisher();
        $content = ['name' => 'Test', 'value' => 123];
        $message = new readonly class($content) implements Message {
            public function __construct(private array $content)
            {
            }

            public function properties(): Set
            {
                return Set::createFrom($this->content);
            }

            public function content(): mixed
            {
                return $this->content;
            }
        };

        $result = $demolisher->extractValues($message);

        $this->assertEquals($content, $result);
    }

    public function testShouldExtractValuesFromExportable(): void
    {
        $demolisher = new Demolisher();
        $data = ['title' => 'Test Title'];
        $exportable = new readonly class($data) implements Exportable {
            public function __construct(private array $data)
            {
            }

            public function export(): array
            {
                return $this->data;
            }
        };

        $result = $demolisher->extractValues($exportable);

        $this->assertEquals($data, $result);
    }

    public function testShouldExtractValuesFromPlainObject(): void
    {
        $demolisher = new Demolisher();
        $object = new readonly class('Test', 123) {
            public function __construct(public string $name, public int $value)
            {
            }
        };

        $result = $demolisher->extractValues($object);

        $this->assertEquals(['name' => 'Test', 'value' => 123], $result);
    }
}
