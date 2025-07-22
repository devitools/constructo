<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Deserialize;

use Constructo\Contract\Exportable;
use Constructo\Core\Deserialize\Demolisher;
use Constructo\Test\Stub\Domain\Collection\Game\FeatureCollection;
use Constructo\Test\Stub\Domain\Entity\Command\GameCommand;
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
}
