<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver\FromTypeDate;
use Constructo\Support\Set;
use Constructo\Type\Timestamp;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class FromTypeDateTest extends TestCase
{
    public function testShouldResolveTimestampType(): void
    {
        $resolver = new FromTypeDate();
        $method = new ReflectionMethod($this, 'methodWithTimestamp');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Timestamp::class, $result->content);
    }

    public function testShouldResolveDateTimeImmutableType(): void
    {
        $resolver = new FromTypeDate();
        $method = new ReflectionMethod($this, 'methodWithDateTimeImmutable');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertInstanceOf(DateTimeImmutable::class, $result->content);
    }

    public function testShouldResolveDateTimeType(): void
    {
        $resolver = new FromTypeDate();
        $method = new ReflectionMethod($this, 'methodWithDateTime');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertInstanceOf(DateTime::class, $result->content);
    }

    public function testShouldResolveDateTimeInterfaceType(): void
    {
        $resolver = new FromTypeDate();
        $method = new ReflectionMethod($this, 'methodWithDateTimeInterface');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertInstanceOf(DateTime::class, $result->content);
    }

    public function testShouldReturnNullForNonDateType(): void
    {
        $resolver = new FromTypeDate();
        $method = new ReflectionMethod($this, 'methodWithNonDateType');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNull($result);
    }

    private function methodWithTimestamp(Timestamp $timestamp): void
    {
    }

    private function methodWithDateTimeImmutable(DateTimeImmutable $date): void
    {
    }

    private function methodWithDateTime(DateTime $date): void
    {
    }

    private function methodWithDateTimeInterface(DateTimeInterface $date): void
    {
    }

    private function methodWithNonDateType(string $value): void
    {
    }
}
