<?php

declare(strict_types=1);

namespace Morph\Test\Core\Serialize\Resolver;

use DateTimeImmutable;
use DateTimeInterface;
use Morph\Core\Serialize\Resolver\AttributeValue;
use Morph\Exception\Adapter\NotResolved;
use Morph\Support\Reflective\Factory\Target;
use Morph\Support\Set;
use Morph\Test\Stub\AttributesVariety;
use PHPUnit\Framework\TestCase;
use Serendipity\Infrastructure\Database\Managed;

use function Morph\Crypt\encrypt;

final class AttributeValueTest extends TestCase
{
    public function testResolveManaged(): void
    {
        $resolver = new AttributeValue();
        $target = Target::createFrom(AttributesVariety::class);
        $parameters = $target->getReflectionParameters();

        [
            $id,
            $createdAt,
        ] = $parameters;

        $expectedId = (new Managed())->id();
        $expectedCreatedAt = (new Managed())->now();
        $set = Set::createFrom([
            'id' => $expectedId,
            'created_at' => $expectedCreatedAt,
        ]);

        $value = $resolver->resolve($id, $set);
        $this->assertEquals($expectedId, $value->content);

        $value = $resolver->resolve($createdAt, $set);
        $this->assertInstanceOf(DateTimeImmutable::class, $value->content);
        $this->assertEquals($expectedCreatedAt, $value->content->format(DateTimeInterface::ATOM));
    }

    public function testResolveDefine(): void
    {
        $resolver = new AttributeValue();
        $target = Target::createFrom(AttributesVariety::class);
        $parameters = $target->getReflectionParameters();

        [, , $email] = $parameters;

        $set = Set::createFrom(['email' => 'w@mail.com']);

        $value = $resolver->resolve($email, $set);
        $this->assertEquals('w@mail.com', $value->content);
    }

    public function testResolvePattern(): void
    {
        $resolver = new AttributeValue();
        $target = Target::createFrom(AttributesVariety::class);
        $parameters = $target->getReflectionParameters();

        $this->assertGreaterThanOrEqual(5, count($parameters));

        [, , , $code, $amount, $precision] = $parameters;

        $set = Set::createFrom([
            'code' => 'ABC123',
            'amount' => '99.99',
            'precision' => '99',
        ]);

        $value = $resolver->resolve($code, $set);
        $this->assertEquals('ABC123', $value->content);

        $value = $resolver->resolve($amount, $set);
        $this->assertEquals(99.99, $value->content);

        $value = $resolver->resolve($precision, $set);
        $this->assertEquals(99, $value->content);
    }

    public function testResolvePatternWithUnionType(): void
    {
        $resolver = new AttributeValue();
        $target = Target::createFrom(AttributesVariety::class);
        $parameters = $target->getReflectionParameters();

        $this->assertGreaterThanOrEqual(6, count($parameters));

        [, , , , , , $variant] = $parameters;

        $set = Set::createFrom(['variant' => '42']);

        $value = $resolver->resolve($variant, $set);
        $this->assertIsNumeric($value->content);
    }

    public function testResolveInvalidPattern(): void
    {
        $resolver = new AttributeValue(path: ['*']);
        $target = Target::createFrom(AttributesVariety::class);
        $parameters = $target->getReflectionParameters();

        $this->assertGreaterThanOrEqual(6, count($parameters));

        [, , , , , , $variant] = $parameters;

        $set = Set::createFrom(['variant' => '42.42']);

        $value = $resolver->resolve($variant, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals(
            "The value given for '*' is not supported.",
            $value->content->message
        );
    }

    public function testPropertyWithNoAttribute(): void
    {
        // Arrange
        $resolver = new AttributeValue(path: ['*']);
        $target = Target::createFrom(AttributesVariety::class);
        $parameters = $target->getReflectionParameters();

        $noAttribute = $parameters[9];
        $set = Set::createFrom(['no_attribute' => 'valor qualquer']);

        // Act
        $value = $resolver->resolve($noAttribute, $set);

        // Assert
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals(
            "The value given for '*' is not supported.",
            $value->content->message
        );
    }

    public function testPropertyWithUnsupportedAttribute(): void
    {
        // Arrange
        $resolver = new AttributeValue(path: ['*']);
        $target = Target::createFrom(AttributesVariety::class);
        $parameters = $target->getReflectionParameters();

        $sensitive = $parameters[7];
        $set = Set::createFrom(['sensitive' => 'senha123']);

        // Act
        $value = $resolver->resolve($sensitive, $set);

        // Assert
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals(
            "The value given for '*' is not supported.",
            $value->content->message
        );
    }

    public function testPropertyWithNoTypeDefinition(): void
    {
        // Arrange
        $resolver = new AttributeValue(path: ['*']);
        $target = Target::createFrom(AttributesVariety::class);
        $parameters = $target->getReflectionParameters();

        $notTyped = $parameters[10];
        $set = Set::createFrom(['not_typed' => 'qualquer valor']);

        // Act
        $value = $resolver->resolve($notTyped, $set);

        // Assert
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals(
            "The value given for '*' is not supported.",
            $value->content->message
        );
    }

    public function testPasswordAttributeWithInvalidValue(): void
    {
        // Arrange
        $resolver = new AttributeValue(path: ['sensitive']);
        $target = Target::createFrom(AttributesVariety::class);
        $parameters = $target->getReflectionParameters();

        $sensitive = $parameters[7];
        $set = Set::createFrom(['sensitive' => 123]);

        // Act
        $value = $resolver->resolve($sensitive, $set);

        // Assert
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals(
            "The value given for 'sensitive' is not supported.",
            $value->content->message
        );
    }

    public function testNoAttributeInvalidValue(): void
    {
        // Arrange
        $resolver = new AttributeValue(path: ['test']);
        $target = Target::createFrom(AttributesVariety::class);
        $parameters = $target->getReflectionParameters();

        $noAttribute = $parameters[9];
        $set = Set::createFrom(['no_attribute' => 123]);

        // Act
        $value = $resolver->resolve($noAttribute, $set);

        // Assert
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals(
            "The value given for 'test' is not supported.",
            $value->content->message
        );
    }

    public function testShouldResolveTypeDefined(): void
    {
        // Arrange
        $resolver = new AttributeValue();
        $target = Target::createFrom(AttributesVariety::class);
        $parameters = $target->getReflectionParameters();

        [, , , , , , , , $sensitive] = $parameters;

        $set = Set::createFrom(['sensitive' => encrypt('123')]);

        // Act
        $value = $resolver->resolve($sensitive, $set);

        // Assert
        $this->assertEquals('123', $value->content);
    }
}
