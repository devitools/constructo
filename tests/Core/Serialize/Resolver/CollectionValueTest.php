<?php

declare(strict_types=1);

namespace Morph\Test\Core\Serialize\Resolver;

use Morph\Core\Serialize\Resolver\CollectionValue;
use Morph\Exception\Adapter\NotResolved;
use Morph\Support\Set;
use Morph\Test\Stub\Domain\Collection\Game\FeatureCollection;
use Morph\Test\Stub\Domain\Entity\Game\Feature;
use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionParameter;
use stdClass;

final class CollectionValueTest extends TestCase
{
    public function testShouldResolveSuccessfully(): void
    {
        // Arrange
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(FeatureCollection::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn('features');

        $faker = $this->faker();
        $set = Set::createFrom([
            'features' => [
                $faker->fake(Feature::class)->toArray(),
                $faker->fake(Feature::class)->toArray(),
            ],
        ]);
        $collectionValue = new CollectionValue();

        // Act
        $result = $collectionValue->resolve($parameter, $set);

        // Assert
        $this->assertInstanceOf(FeatureCollection::class, $result->content);
        $this->assertCount(2, $result->content);
    }

    public function testShouldResolveWhenTheValueIsNotValid(): void
    {
        // Arrange
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(FeatureCollection::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn('features');

        $set = Set::createFrom([
            'features' => 0,
        ]);
        $collectionValue = new CollectionValue();

        // Act
        $result = $collectionValue->resolve($parameter, $set);

        // Assert
        $this->assertInstanceOf(FeatureCollection::class, $result->content);
        $this->assertCount(0, $result->content);
    }

    public function testShouldNotResolveCollectionWhenParameterIsNotCollection(): void
    {
        // Arrange
        $chain = new CollectionValue();
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(stdClass::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $set = Set::createFrom([]);

        // Act
        $result = $chain->resolve($parameter, $set);

        // Assert
        $this->assertInstanceOf(NotResolved::class, $result->content);
    }
}
