<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Deserialize\Resolve;

use Constructo\Core\Deserialize\Resolve\AttributeChain;
use Constructo\Support\Reflective\Attribute\Define;
use Constructo\Support\Reflective\Attribute\Managed;
use Constructo\Support\Reflective\Attribute\Pattern;
use Constructo\Support\Reflective\Definition\Type;
use Constructo\Test\Stub\Type\Sensitive;
use Constructo\Type\Timestamp;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

use function Constructo\Crypt\decrypt;

final class AttributeChainTest extends TestCase
{
    public function testResolveWithoutType(): void
    {
        $chain = new AttributeChain();
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->method('getType')->willReturn(null);

        $value = 'test';
        $result = $chain->resolve($parameter, $value);

        $this->assertEquals('test', $result->content);
    }

    public function testResolveWithManagedAttributeId(): void
    {
        // Arrange
        $chain = new AttributeChain();
        $managed = $this->createMock(ReflectionAttribute::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $namedType = $this->createMock(ReflectionNamedType::class);

        // Mock
        $managed->expects($this->once())
            ->method('newInstance')
            ->willReturn(new Managed('id'));
        $parameter->method('getType')
            ->willReturn($namedType);
        $namedType->method('getName')
            ->willReturn('string');
        $parameter->expects($this->once())
            ->method('getAttributes')
            ->willReturn([$managed]);

        // Act
        $value = 'uuid-123';
        $result = $chain->resolve($parameter, $value);

        // Assert
        $this->assertEquals('uuid-123', $result->content);
    }

    #[TestWith([new Timestamp()])]
    #[TestWith([new DateTimeImmutable()])]
    #[TestWith([new DateTime()])]
    public function testResolveWithManagedAttributeNow(DateTimeInterface $value): void
    {
        // Arrange
        $chain = new AttributeChain();
        $managed = $this->createMock(ReflectionAttribute::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $namedType = $this->createMock(ReflectionNamedType::class);

        // Mock
        $managed->expects($this->once())
            ->method('newInstance')
            ->willReturn(new Managed('timestamp'));
        $parameter->method('getType')
            ->willReturn($namedType);
        $namedType->method('getName')
            ->willReturn(DateTimeImmutable::class);
        $parameter->expects($this->once())
            ->method('getAttributes')
            ->willReturn([$managed]);

        // Act
        $result = $chain->resolve($parameter, $value);

        // Assert
        $expected = $value->format(DateTimeInterface::ATOM);
        $actual = $result->content;
        $this->assertEquals($expected, $actual);
    }

    #[TestWith([Type::EMOJI, '😀'])]
    #[TestWith([Type::URL, 'https://example.com'])]
    public function testResolveWithDefineType(Type $type, string $value): void
    {
        // Arrange
        $chain = new AttributeChain();
        $defineAttribute = $this->createMock(ReflectionAttribute::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $namedType = $this->createMock(ReflectionNamedType::class);

        // Mock
        $defineAttribute->expects($this->once())
            ->method('newInstance')
            ->willReturn(new Define($type));
        $parameter->method('getType')
            ->willReturn($namedType);
        $namedType->method('getName')
            ->willReturn('string');
        $parameter->expects($this->once())
            ->method('getAttributes')
            ->willReturn([$defineAttribute]);

        // Act
        $result = $chain->resolve($parameter, $value);

        // Assert
        $this->assertEquals($value, $result->content);
    }

    public function testResolveWithDefineTypeExtended(): void
    {
        // Arrange
        $chain = new AttributeChain();
        $defineAttribute = $this->createMock(ReflectionAttribute::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $namedType = $this->createMock(ReflectionNamedType::class);

        // Mock
        $defineAttribute->expects($this->once())
            ->method('newInstance')
            ->willReturn(new Define(new Sensitive()));
        $parameter->method('getType')
            ->willReturn($namedType);
        $namedType->method('getName')
            ->willReturn('string');
        $parameter->expects($this->once())
            ->method('getAttributes')
            ->willReturn([$defineAttribute]);

        // Act
        $result = $chain->resolve($parameter, '123');

        // Assert
        $this->assertEquals('123', decrypt($result->content));
    }

    public function testResolveWithPattern(): void
    {
        // Arrange
        $chain = new AttributeChain();
        $patternAttribute = $this->createMock(ReflectionAttribute::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $namedType = $this->createMock(ReflectionNamedType::class);

        // Mock
        $patternAttribute->expects($this->once())
            ->method('newInstance')
            ->willReturn(new Pattern('/\d+/'));
        $parameter->method('getType')
            ->willReturn($namedType);
        $namedType->method('getName')
            ->willReturn('string');
        $parameter->expects($this->once())
            ->method('getAttributes')
            ->willReturn([$patternAttribute]);

        // Act
        $value = '123';
        $result = $chain->resolve($parameter, $value);

        // Assert
        $this->assertEquals('123', $result->content);
    }

    public function testResolveWithUnionTypeAndPattern(): void
    {
        // Arrange
        $chain = new AttributeChain();
        $patternAttribute = $this->createMock(ReflectionAttribute::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $unionType = $this->createMock(ReflectionUnionType::class);
        $namedType = $this->createMock(ReflectionNamedType::class);

        // Mock
        $patternAttribute->expects($this->once())
            ->method('newInstance')
            ->willReturn(new Pattern('/\d+/'));
        $parameter->method('getType')
            ->willReturn($unionType);
        $unionType->method('getTypes')
            ->willReturn([$namedType]);
        $namedType->method('getName')
            ->willReturn('string');
        $parameter->expects($this->once())
            ->method('getAttributes')
            ->willReturn([$patternAttribute]);

        // Act
        $value = '123';
        $result = $chain->resolve($parameter, $value);

        // Assert
        $this->assertEquals('123', $result->content);
    }

    public function testResolveWithMultipleAttributes(): void
    {
        // Arrange
        $chain = new AttributeChain();
        $patternAttribute = $this->createMock(ReflectionAttribute::class);
        $managedAttribute = $this->createMock(ReflectionAttribute::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $namedType = $this->createMock(ReflectionNamedType::class);

        // Mock
        $patternAttribute->expects($this->once())
            ->method('newInstance')
            ->willReturn(new Pattern('/\d+/'));
        $managedAttribute->expects($this->never())
            ->method('newInstance');
        $parameter->method('getType')
            ->willReturn($namedType);
        $namedType->method('getName')
            ->willReturn('string');
        $parameter->expects($this->once())
            ->method('getAttributes')
            ->willReturn([$patternAttribute, $managedAttribute]);

        // Act
        $value = '123';
        $result = $chain->resolve($parameter, $value);

        // Assert
        $this->assertEquals('123', $result->content);
    }
}
