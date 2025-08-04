<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Support\Reflective\Notation;
use Constructo\Support\Set;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Test\Stub\Command;
use Constructo\Test\Stub\EntityManaged;
use Constructo\Test\Stub\PatternMock;
use Constructo\Test\Stub\Variety;
use Constructo\Core\Fake\Resolver\FromTypeAttributes;

final class FromTypeAttributesTest extends TestCase
{
    public function testShouldResolveEmailAttribute(): void
    {
        $resolver = new FromTypeAttributes(Notation::SNAKE);
        $target = Target::createFrom(Command::class);
        $parameters = $target->getReflectionParameters();

        [0 => $emailParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($emailParameter, $set);

        $this->assertNotNull($value);
        $this->assertIsString($value->content);
        $this->assertStringContainsString('@', $value->content);
    }

    public function testShouldResolveIpAddressAttribute(): void
    {
        $resolver = new FromTypeAttributes(Notation::SNAKE);
        $target = Target::createFrom(Command::class);
        $parameters = $target->getReflectionParameters();

        [1 => $ipAddressParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($ipAddressParameter, $set);

        $this->assertNotNull($value);
        $this->assertIsString($value->content);
        $this->assertMatchesRegularExpression(
            '/^(\d{1,3}\.){3}\d{1,3}$|^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$/',
            $value->content
        );
    }

    public function testShouldResolveFirstNameAttribute(): void
    {
        $resolver = new FromTypeAttributes(Notation::SNAKE);
        $target = Target::createFrom(Command::class);
        $parameters = $target->getReflectionParameters();

        [4 => $firstNameParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($firstNameParameter, $set);

        $this->assertNotNull($value);
        $this->assertIsString($value->content);
    }

    public function testShouldResolveTypeExtendedAttribute(): void
    {
        $resolver = new FromTypeAttributes(Notation::SNAKE);
        $target = Target::createFrom(Command::class);
        $parameters = $target->getReflectionParameters();

        [
            5 => $password,
            6 => $address,
        ] = $parameters;

        $set = Set::createFrom([]);

        $value = $resolver->resolve($password, $set);
        $this->assertNotNull($value);
        $this->assertIsString($value->content);

        $value = $resolver->resolve($address, $set);
        $this->assertNull($value);
    }

    public function testShouldFallbackToNextResolverWhenNoAttribute(): void
    {
        $resolver = new FromTypeAttributes(Notation::SNAKE);
        $target = Target::createFrom(Command::class);
        $parameters = $target->getReflectionParameters();

        [2 => $signupDateParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($signupDateParameter, $set);

        $this->assertNull($value);
    }

    public function testShouldReturnNullForUndefinedType(): void
    {
        $resolver = new FromTypeAttributes(Notation::SNAKE);
        $target = Target::createFrom(Variety::class);
        $parameters = $target->getReflectionParameters();

        [3 => $whateverParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($whateverParameter, $set);

        $this->assertNull($value);
    }

    public function testShouldResolveManagedAttributes(): void
    {
        $resolver = new FromTypeAttributes(Notation::SNAKE);
        $target = Target::createFrom(EntityManaged::class);
        $parameters = $target->getReflectionParameters();

        [0 => $idParameter] = $parameters;
        [1 => $createdAtParameter] = $parameters;

        $set = Set::createFrom([]);

        $idValue = $resolver->resolve($idParameter, $set);
        $this->assertNotNull($idValue);
        $this->assertIsString($idValue->content);

        $createdAtValue = $resolver->resolve($createdAtParameter, $set);
        $this->assertNotNull($createdAtValue);
        $this->assertIsString(DateTimeImmutable::class, $createdAtValue->content);
    }

    public function testShouldDetectTypeBeforeFakePattern(): void
    {
        $resolver = new FromTypeAttributes(Notation::SNAKE);
        $target = Target::createFrom(PatternMock::class);
        $parameters = $target->getReflectionParameters();

        [
            $id,
            $name,
            $code,
            $amount,
        ] = $parameters;

        $value = $resolver->resolve($id, Set::createFrom([]));
        $this->assertNotNull($value);
        $this->assertIsInt($value->content);

        $value = $resolver->resolve($name, Set::createFrom([]));
        $this->assertNotNull($value);
        $this->assertIsString($value->content);

        $value = $resolver->resolve($code, Set::createFrom([]));
        $this->assertNotNull($value);
        $this->assertIsNumeric($value->content);

        $value = $resolver->resolve($amount, Set::createFrom([]));
        $this->assertNotNull($value);
        $this->assertIsFloat($value->content);
    }
}
