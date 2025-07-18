<?php

declare(strict_types=1);

namespace Morph\Test\Core\Serialize\Resolver;

use Morph\Core\Serialize\Resolver\ValidateValue;
use Morph\Exception\Adapter\NotResolved;
use Morph\Support\Reflective\Factory\Target;
use Morph\Support\Set;
use Morph\Test\Stub\Builtin;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

final class ValidateValueTest extends TestCase
{
    public function testShouldValidateValueRequired(): void
    {
        $resolver = new ValidateValue(path: ['string']);
        $target = Target::createFrom(Builtin::class);
        $parameters = $target->getReflectionParameters();

        $this->assertCount(6, $parameters);

        $set = Set::createFrom([]);

        [0 => $string] = $parameters;

        $value = $resolver->resolve($string, $set);
        $this->assertInstanceOf(NotResolved::class, $value->content);
        $this->assertEquals("The value for 'string' is required and was not given.", $value->content->message);
    }

    public function testShouldValidateValueMismatch(): void
    {
        // Arrange
        $set = Set::createFrom([
            'first' => '10',
            'second' => new stdClass(),
            'third' => true,
            'fourth' => null,
        ]);

        $target = new ReflectionClass(new class (1, 2, 3) {
            public function __construct(
                public int $first,
                public float $second,
                public string|int $third,
                public string $fourth = 'default',
            ) {
            }
        });
        $parameters = $target->getConstructor()->getParameters();

        $errors = [
            'first' => "The value for 'first' must be of type 'int' and 'string' was given.",
            'second' => "The value for 'second' must be of type 'float' and 'stdClass' was given.",
            'third' => "The value for 'third' must be of type 'int|string' and 'bool' was given.",
            'fourth' => "The value for 'fourth' must be of type 'string' and 'null' was given.",
        ];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $resolver = new ValidateValue(path: [$name]);
            $result = $resolver->resolve($parameter, $set);
            $this->assertInstanceOf(NotResolved::class, $result->content);
            $this->assertEquals(
                $errors[$name],
                $result->content->message
            );
        }
    }
}
