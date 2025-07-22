<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Serialize\Resolver;

use Constructo\Contract\Formatter;
use Constructo\Core\Serialize\Resolver\FormatValue;
use Constructo\Exception\Adapter\NotResolved;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Support\Set;
use Constructo\Test\Stub\Builtin;
use Constructo\Test\Stub\EntityStub;
use Constructo\Test\Stub\NoConstructor;
use Constructo\Test\Stub\Type\Intersected;
use Constructo\Test\Stub\Variety;
use PHPUnit\Framework\TestCase;
use stdClass;

final class FormatValueTest extends TestCase
{
    public function testFormatValueBuiltinSuccessfully(): void
    {
        $formatters = [
            'string' => new class implements Formatter {
                public function format(mixed $value, mixed $option = null): string
                {
                    return (string) $value;
                }
            },
            'int' => new class implements Formatter {
                public function format(mixed $value, mixed $option = null): int
                {
                    return (int) $value;
                }
            },
            'float' => fn () => new stdClass(),
        ];
        $resolver = new FormatValue(formatters: $formatters, path: ['*']);
        $target = Target::createFrom(Builtin::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(6, $parameters);

        $set = Set::createFrom([
            'string' => 10,
            'int' => '10',
            'float' => ['val' => 10.1],
            'bool' => true,
        ]);

        [
            $string,
            $int,
            $float,
            $bool,
        ] = $parameters;

        $value = $resolver->resolve($string, $set);
        $this->assertSame('10', $value->content);

        $value = $resolver->resolve($int, $set);
        $this->assertSame(10, $value->content);

        $value = $resolver->resolve($float, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals("The value given for '*' is not supported.", $value->content->message);

        $value = $resolver->resolve($bool, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals("The value given for '*' is not supported.", $value->content->message);
    }

    public function testTypeMatchedShouldResolveVariety(): void
    {
        $formatters = [
            'int|string' => fn (array $value) => array_shift($value),
            'Countable&Iterator' => fn () => new Intersected(),
            EntityStub::class => fn (array $value) => new EntityStub(...$value),
        ];
        $resolver = new FormatValue(formatters: $formatters, path: ['*']);
        $target = Target::createFrom(Variety::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(4, $parameters);

        $set = Set::createFrom([
            'union' => [true, 'string', 10],
            'intersection' => null,
            'nested' => [
                10,
                10.1,
                'string',
                true,
                new NoConstructor(),
                null,
                null,
            ],
            'whatever' => new stdClass(),
        ]);

        [
            $union,
            $intersection,
            $nested,
            $whatever,
        ] = $parameters;

        $value = $resolver->resolve($union, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals("The value given for '*' is not supported.", $value->content->message);

        $value = $resolver->resolve($intersection, $set);
        $this->assertInstanceOf(Intersected::class, $value->content);

        $value = $resolver->resolve($nested, $set);
        $this->assertInstanceOf(EntityStub::class, $value->content);

        $value = $resolver->resolve($whatever, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals("The value given for '*' is not supported.", $value->content->message);
    }
}
