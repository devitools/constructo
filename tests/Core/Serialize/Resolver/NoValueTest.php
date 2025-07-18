<?php

declare(strict_types=1);

namespace Morph\Test\Core\Serialize\Resolver;

use Morph\Core\Serialize\Resolver\NoValue;
use Morph\Support\Reflective\Factory\Target;
use Morph\Support\Set;
use Morph\Test\Stub\NullableAndOptional;
use PHPUnit\Framework\TestCase;

final class NoValueTest extends TestCase
{
    public function testNoValueSuccessfully(): void
    {
        $resolver = new NoValue();
        $target = Target::createFrom(NullableAndOptional::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(3, $parameters);

        $empty = Set::createFrom([]);

        [
            $nullable,
            $union,
            $optional,
        ] = $parameters;

        $value = $resolver->resolve($nullable, $empty);
        $this->assertNull($value->content);

        $value = $resolver->resolve($union, $empty);
        $this->assertNull($value->content);

        $value = $resolver->resolve($optional, $empty);
        $this->assertSame(10, $value->content);
    }
}
