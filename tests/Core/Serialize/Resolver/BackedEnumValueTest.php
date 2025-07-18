<?php

declare(strict_types=1);

namespace Morph\Test\Core\Serialize\Resolver;

use Morph\Core\Serialize\Resolver\BackedEnumValue;
use Morph\Exception\Adapter\NotResolved;
use Morph\Support\Reflective\Factory\Target;
use Morph\Support\Set;
use Morph\Test\Stub\EnumVariety;
use Morph\Test\Stub\NotNative;
use Morph\Test\Stub\Type\BackedEnumeration;
use Morph\Test\Stub\Type\Enumeration;
use Morph\Test\Stub\Type\SingleBacked;
use PHPUnit\Framework\TestCase;

final class BackedEnumValueTest extends TestCase
{
    public function testShouldHandleBackedEnumValue(): void
    {
        $resolver = new BackedEnumValue();
        $target = Target::createFrom(NotNative::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(3, $parameters);

        [
            $backed,
            $enum,
        ] = $parameters;

        $set = Set::createFrom([
            'backed' => BackedEnumeration::BAR->value,
            'enum' => Enumeration::TWO,
        ]);

        $value = $resolver->resolve($backed, $set);
        $this->assertEquals(BackedEnumeration::BAR, $value->content);

        $value = $resolver->resolve($enum, $set);
        $this->assertEquals(Enumeration::TWO, $value->content);
    }

    public function testShouldNotResolveInvalidValue(): void
    {
        $resolver = new BackedEnumValue();
        $target = Target::createFrom(NotNative::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(3, $parameters);

        [
            $backed,
            $enum,
            $stub,
        ] = $parameters;

        $set = Set::createFrom([
            'backed' => true,
            'enum' => 'TWO',
        ]);

        $value = $resolver->resolve($backed, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);

        $value = $resolver->resolve($enum, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);

        $value = $resolver->resolve($stub, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
    }

    public function testShouldNotResolveInvalidUnionAndIntersection(): void
    {
        $resolver = new BackedEnumValue();
        $target = Target::createFrom(EnumVariety::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(4, $parameters);

        [
            $enum,
            $backed,
            $union,
            $intersection,
        ] = $parameters;

        $set = Set::createFrom([
            'enum' => Enumeration::TWO,
            'backed' => BackedEnumeration::BAR->value,
            'union' => Enumeration::ONE,
            'intersection' => BackedEnumeration::BAR,
        ]);

        $value = $resolver->resolve($enum, $set);
        $this->assertInstanceOf(Enumeration::class, $value->content);

        $value = $resolver->resolve($backed, $set);
        $this->assertInstanceOf(BackedEnumeration::class, $value->content);

        $value = $resolver->resolve($union, $set);
        $this->assertEquals(Enumeration::ONE, $value->content);

        $value = $resolver->resolve($intersection, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
    }

    public function testShouldNotResolveTypeMismatch(): void
    {
        $resolver = new BackedEnumValue();
        $target = Target::createFrom(NotNative::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(3, $parameters);

        [
            $backed,
            $enum,
        ] = $parameters;

        $set = Set::createFrom([
            'backed' => 1,
            'enum' => SingleBacked::ONE,
        ]);

        $value = $resolver->resolve($backed, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);

        $value = $resolver->resolve($enum, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
    }
}
