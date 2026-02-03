<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Serialize\Resolver;

use Constructo\Core\Serialize\Resolver\CollectionValue;
use Constructo\Exception\Adapter\NotResolved;
use Constructo\Support\Set;
use Constructo\Test\Stub\Domain\Collection\Game\FeatureCollection;
use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionParameter;
use stdClass;

final class CollectionValueTest extends TestCase
{
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

    public function testShouldResolveCollectionWithArrayData(): void
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
            'features' => [
                ['name' => 'feature1', 'description' => 'First feature', 'enabled' => true],
                ['name' => 'feature2', 'description' => 'Second feature', 'enabled' => false],
            ],
        ]);
        $collectionValue = new CollectionValue();

        // Act
        $result = $collectionValue->resolve($parameter, $set);

        // Assert
        $this->assertInstanceOf(FeatureCollection::class, $result->content);
        $this->assertCount(2, $result->content);
    }

    public function testShouldReturnSameCollectionWhenValueIsAlreadyCollectionInstance(): void
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

        $existingCollection = new FeatureCollection();
        $set = Set::createFrom([
            'features' => $existingCollection,
        ]);
        $collectionValue = new CollectionValue();

        // Act
        $result = $collectionValue->resolve($parameter, $set);

        // Assert
        $this->assertSame($existingCollection, $result->content);
    }
}
