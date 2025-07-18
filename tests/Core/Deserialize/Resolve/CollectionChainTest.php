<?php

declare(strict_types=1);

namespace Morph\Test\Core\Deserialize\Resolve;

use Morph\Core\Deserialize\Resolve\CollectionChain;
use Morph\Test\Stub\Domain\Collection\Game\FeatureCollection;
use Morph\Test\Stub\Domain\Collection\GameCollection;
use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionParameter;
use stdClass;

final class CollectionChainTest extends TestCase
{
    public function testShouldResolveCollectionSuccessfully(): void
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

        $chain = new CollectionChain();
        $collection = new FeatureCollection();

        // Act
        $result = $chain->resolve($parameter, $collection);

        // Assert
        $this->assertEquals([], $result->content);
    }

    public function testShouldNotResolveCollectionWhenParameterIsNotCollection(): void
    {
        // Arrange
        $chain = new CollectionChain();
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(stdClass::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $collection = new FeatureCollection();

        // Act
        $result = $chain->resolve($parameter, $collection);

        // Assert
        $this->assertEquals($collection, $result->content);
    }

    public function testShouldNotResolveCollectionWhenParameterTypeNotMatch(): void
    {
        // Arrange
        $chain = new CollectionChain();
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(GameCollection::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $collection = new FeatureCollection();

        // Act
        $result = $chain->resolve($parameter, $collection);

        // Assert
        $this->assertEquals($collection, $result->content);
    }
}
