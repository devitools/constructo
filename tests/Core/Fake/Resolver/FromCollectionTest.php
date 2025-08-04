<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Support\Set;
use Constructo\Test\Stub\Domain\Collection\Game\FeatureCollection;
use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionParameter;
use Constructo\Core\Fake\Resolver\FromCollection;
use stdClass;

final class FromCollectionTest extends TestCase
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

        $fromCollection = new FromCollection();
        $presets = Set::createFrom([]);

        // Act
        $result = $fromCollection->resolve($parameter, $presets);

        // Assert
        $this->assertIsArray($result->content);
        $this->assertNotEmpty($result->content);
    }


    public function testShouldNotResolveCollectionWhenParameterIsNotCollection(): void
    {
        // Arrange
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())
            ->method('getName')
            ->willReturn(stdClass::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $set = Set::createFrom([]);
        $fromCollection = new FromCollection();

        // Act
        $result = $fromCollection->resolve($parameter, $set);

        // Assert
        $this->assertNull($result);
    }
}
