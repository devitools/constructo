<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver\FromDefaultValue;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Support\Reflective\Notation;
use Constructo\Support\Set;
use Constructo\Test\Stub\Builtin;
use Constructo\Test\Stub\Command;
use Constructo\Test\Stub\NullableAndOptional;
use PHPUnit\Framework\TestCase;

final class FromDefaultValueTest extends TestCase
{
    public function testShouldResolveParameterWithDefaultValue(): void
    {
        $resolver = new FromDefaultValue(Notation::SNAKE);
        $target = Target::createFrom(NullableAndOptional::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(3, $parameters);

        [2 => $optional] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($optional, $set);

        $this->assertNotNull($value);
        $this->assertEquals(10, $value->content);
    }

    public function testShouldResolveOptionalParameter(): void
    {
        $resolver = new FromDefaultValue(Notation::SNAKE);
        $target = Target::createFrom(Command::class);
        $parameters = $target->getReflectionParameters();

        // A classe Command tem vários parâmetros opcionais
        [6 => $address] = $parameters; // O parâmetro 'address' é opcional e permite null

        $set = Set::createFrom([]);
        $value = $resolver->resolve($address, $set);

        $this->assertNotNull($value);
        $this->assertNull($value->content);
    }

    public function testShouldResolveNullableParameter(): void
    {
        $resolver = new FromDefaultValue(Notation::SNAKE);
        $target = Target::createFrom(NullableAndOptional::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(3, $parameters);

        [0 => $nullable] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($nullable, $set);

        $this->assertNotNull($value);
        $this->assertNull($value->content);
    }

    public function testShouldFallbackToNextResolver(): void
    {
        $resolver = new FromDefaultValue(Notation::SNAKE);
        $target = Target::createFrom(Builtin::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(6, $parameters);

        [0 => $string] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($string, $set);

        $this->assertNull($value);
    }
}
