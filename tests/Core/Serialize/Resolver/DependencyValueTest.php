<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Serialize\Resolver;

use Constructo\Core\Serialize\Resolver\DependencyValue;
use Constructo\Exception\Adapter\NotResolved;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Support\Set;
use Constructo\Test\Stub\Builtin;
use Constructo\Test\Stub\Command;
use Constructo\Test\Stub\Complex;
use Constructo\Test\Stub\EntityStub;
use Constructo\Test\Stub\Intersection;
use Constructo\Test\Stub\Native;
use Constructo\Test\Stub\NoConstructor;
use Constructo\Test\Stub\Union;
use DateTime;
use DateTimeImmutable;
use Faker\Factory;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use stdClass;

final class DependencyValueTest extends TestCase
{
    #[TestWith(['2021-01-01'])]
    #[TestWith([new DateTimeImmutable('2021-01-01')])]
    public function testShouldHandleDependency(mixed $value): void
    {
        $resolver = new DependencyValue();
        $target = Target::createFrom(Command::class);
        $parameters = $target->getReflectionParameters();

        $set = Set::createFrom(['signup_date' => $value]);

        [
            2 => $signupDate,
            13 => $dob,
        ] = $parameters;

        $resolved = $resolver->resolve($signupDate, $set);
        $this->assertInstanceOf(DateTimeImmutable::class, $resolved->content);

        $resolved = $resolver->resolve($dob, $set);
        $this->assertInstanceOf(NotResolved::class, $resolved->content);
    }

    public function testShouldHandleDependencyComplex(): void
    {
        $resolver = new DependencyValue();
        $target = Target::createFrom(Complex::class);
        $parameters = $target->getReflectionParameters();

        $generator = Factory::create();
        $set = Set::createFrom([
            'entity' => [
                'id' => $generator->numberBetween(1, 100),
                'price' => $generator->randomFloat(),
                'name' => $generator->name(),
                'is_active' => $generator->boolean(),
                'more' => new NoConstructor(),
            ],
            'native' => [
                'callable' => fn () => null,
                'std_class' => new stdClass(),
                'date_time_immutable' => new DateTimeImmutable(),
                'date_time' => '2021-01-01',
                'date_time_interface' => new DateTime('2021-01-01'),
            ],
            'builtin' => [
                $generator->word(),
                $generator->numberBetween(1, 100),
                $generator->randomFloat(),
                $generator->boolean(),
                $generator->words(),
                null,
            ],
        ]);

        [
            $entity,
            $native,
            $builtin,
        ] = $parameters;

        $resolved = $resolver->resolve($entity, $set);
        $this->assertInstanceOf(EntityStub::class, $resolved->content);

        $resolved = $resolver->resolve($native, $set);
        $this->assertInstanceOf(Native::class, $resolved->content);

        $resolved = $resolver->resolve($builtin, $set);
        $this->assertInstanceOf(Builtin::class, $resolved->content);
    }

    public function testShouldHandleDependencyUnion(): void
    {
        $resolver = new DependencyValue();
        $target = Target::createFrom(Union::class);
        $parameters = $target->getReflectionParameters();

        $set = Set::createFrom(['native' => new stdClass()]);

        [2 => $native] = $parameters;

        $resolved = $resolver->resolve($native, $set);
        $this->assertInstanceOf(stdClass::class, $resolved->content);
    }

    public function testShouldHandleDependencyIntersection(): void
    {
        $resolver = new DependencyValue();
        $target = Target::createFrom(Intersection::class);
        $parameters = $target->getReflectionParameters();

        $set = Set::createFrom(['intersected' => null]);

        [$intersected] = $parameters;

        $resolved = $resolver->resolve($intersected, $set);
        $this->assertInstanceOf(NotResolved::class, $resolved->content);
    }
}
