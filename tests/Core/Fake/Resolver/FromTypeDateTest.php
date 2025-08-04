<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Support\Reflective\Notation;
use Constructo\Support\Set;
use Constructo\Type\Timestamp;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Test\Stub\Native;
use Constructo\Test\Stub\Variety;
use Constructo\Core\Fake\Resolver\FromTypeDate;
use ReflectionMethod;

final class FromTypeDateTest extends TestCase
{
    public function testShouldResolveTimestamp(): void
    {
        $resolver = new FromTypeDate(Notation::SNAKE);
        $method = new ReflectionMethod($this, 'methodWithTimestamp');
        $parameter = $method->getParameters()[0];
        $set = Set::createFrom([]);

        $value = $resolver->resolve($parameter, $set);

        $this->assertNotNull($value);
        $this->assertInstanceOf(Timestamp::class, $value->content);
    }

    public function testShouldResolveDateTimeImmutable(): void
    {
        $resolver = new FromTypeDate(Notation::SNAKE);
        $target = Target::createFrom(Native::class);
        $parameters = $target->getReflectionParameters();

        [2 => $dateTimeImmutableParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($dateTimeImmutableParameter, $set);

        $this->assertNotNull($value);
        $this->assertInstanceOf(DateTimeImmutable::class, $value->content);
    }

    public function testShouldResolveDateTime(): void
    {
        $resolver = new FromTypeDate(Notation::SNAKE);
        $target = Target::createFrom(Native::class);
        $parameters = $target->getReflectionParameters();

        [3 => $dateTimeParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($dateTimeParameter, $set);

        $this->assertNotNull($value);
        $this->assertInstanceOf(DateTime::class, $value->content);
    }

    public function testShouldResolveDateTimeInterface(): void
    {
        $resolver = new FromTypeDate(Notation::SNAKE);
        $target = Target::createFrom(Native::class);
        $parameters = $target->getReflectionParameters();

        [4 => $dateTimeInterfaceParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($dateTimeInterfaceParameter, $set);

        $this->assertNotNull($value);
        $this->assertInstanceOf(DateTimeInterface::class, $value->content);
    }

    public function testShouldNotResolveFallbackToNextResolverForNonNativeType(): void
    {
        $resolver = new FromTypeDate(Notation::SNAKE);
        $target = Target::createFrom(Variety::class);
        $parameters = $target->getReflectionParameters();

        [2 => $entityStubParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($entityStubParameter, $set);

        $this->assertNull($value);
    }

    public function testShouldReturnNullForParameterWithoutType(): void
    {
        $resolver = new FromTypeDate(Notation::SNAKE);
        $target = Target::createFrom(Variety::class);
        $parameters = $target->getReflectionParameters();

        [3 => $whateverParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($whateverParameter, $set);

        $this->assertNull($value);
    }

    private function methodWithTimestamp(Timestamp $timestamp): void
    {
    }
}
