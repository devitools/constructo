<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Support\Reflective\Notation;
use Constructo\Support\Set;
use PHPUnit\Framework\TestCase;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Test\Stub\Builtin;
use Constructo\Test\Stub\Variety;
use Constructo\Core\Fake\Resolver\FromTypeBuiltin;

final class FromTypeBuiltinTest extends TestCase
{
    public function testShouldResolveStringType(): void
    {
        $resolver = new FromTypeBuiltin(Notation::SNAKE);
        $target = Target::createFrom(Builtin::class);
        $parameters = $target->getReflectionParameters();

        [0 => $stringParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($stringParameter, $set);

        $this->assertNotNull($value);
        $this->assertIsString($value->content);
    }

    public function testShouldResolveIntType(): void
    {
        $resolver = new FromTypeBuiltin(Notation::SNAKE);
        $target = Target::createFrom(Builtin::class);
        $parameters = $target->getReflectionParameters();

        [1 => $intParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($intParameter, $set);

        $this->assertNotNull($value);
        $this->assertIsInt($value->content);
        $this->assertGreaterThanOrEqual(1, $value->content);
        $this->assertLessThanOrEqual(100, $value->content);
    }

    public function testShouldResolveFloatType(): void
    {
        $resolver = new FromTypeBuiltin(Notation::SNAKE);
        $target = Target::createFrom(Builtin::class);
        $parameters = $target->getReflectionParameters();

        [2 => $floatParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($floatParameter, $set);

        $this->assertNotNull($value);
        $this->assertIsFloat($value->content);
        $this->assertGreaterThanOrEqual(1, $value->content);
        $this->assertLessThanOrEqual(100, $value->content);
    }

    public function testShouldResolveBoolType(): void
    {
        $resolver = new FromTypeBuiltin(Notation::SNAKE);
        $target = Target::createFrom(Builtin::class);
        $parameters = $target->getReflectionParameters();

        [3 => $boolParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($boolParameter, $set);

        $this->assertNotNull($value);
        $this->assertIsBool($value->content);
    }

    public function testShouldResolveArrayType(): void
    {
        $resolver = new FromTypeBuiltin(Notation::SNAKE);
        $target = Target::createFrom(Builtin::class);
        $parameters = $target->getReflectionParameters();

        [4 => $arrayParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($arrayParameter, $set);

        $this->assertNotNull($value);
        $this->assertIsArray($value->content);
    }

    public function testShouldFallbackToNextResolverForNonBuiltinType(): void
    {
        $resolver = new FromTypeBuiltin(Notation::SNAKE);
        $target = Target::createFrom(Variety::class);
        $parameters = $target->getReflectionParameters();

        [2 => $nestedParameter] = $parameters; // EntityStub não é um tipo built-in

        $set = Set::createFrom([]);
        $value = $resolver->resolve($nestedParameter, $set);

        $this->assertNull($value);
    }

    public function testShouldReturnNullForNullType(): void
    {
        $resolver = new FromTypeBuiltin(Notation::SNAKE);
        $target = Target::createFrom(Builtin::class);
        $parameters = $target->getReflectionParameters();

        [5 => $nullParameter] = $parameters;

        $set = Set::createFrom([]);
        $value = $resolver->resolve($nullParameter, $set);

        $this->assertNull($value);
    }

    public function testShouldReturnNullForParameterWithoutType(): void
    {
        $resolver = new FromTypeBuiltin(Notation::SNAKE);
        $target = Target::createFrom(Variety::class);
        $parameters = $target->getReflectionParameters();

        [3 => $whateverParameter] = $parameters; // Parâmetro sem tipo

        $set = Set::createFrom([]);
        $value = $resolver->resolve($whateverParameter, $set);

        $this->assertNull($value);
    }
}
