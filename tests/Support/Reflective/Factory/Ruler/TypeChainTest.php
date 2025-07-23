<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Reflective\Factory\Ruler;

use Constructo\Support\Reflective\Factory\Ruler\TypeChain;
use Constructo\Support\Reflective\Ruleset;
use Constructo\Test\Stub\AttributesVariety;
use Constructo\Test\Stub\Builtin;
use Constructo\Test\Stub\Command;
use Constructo\Test\Stub\Complex;
use Constructo\Test\Stub\DeepDeepDown;
use Constructo\Test\Stub\EnumVariety;
use Constructo\Test\Stub\Variety;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TypeChainTest extends TestCase
{
    /**
     * @dataProvider typeMapProvider
     */
    public function testBuiltinTypeResolution(string $typeName, string $ruleName): void
    {
        $chain = new TypeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(Builtin::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = null;
        foreach ($parameters as $param) {
            if ($param->getName() === $typeName) {
                $parameter = $param;
                break;
            }
        }

        $this->assertNotNull($parameter, sprintf("Parameter '%s' not found", $typeName));

        $chain->resolve($parameter, $ruleset);

        $this->assertEquals([$ruleName], $ruleset->get($typeName));
    }

    public static function typeMapProvider(): array
    {
        return [
            'array' => ['array', 'array'],
            'bool' => ['bool', 'boolean'],
            'integer' => ['int', 'integer'],
            'float' => ['float', 'numeric'],
            'string' => ['string', 'string'],
        ];
    }

    public function testBackedEnumResolution(): void
    {
        $chain = new TypeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(Command::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = $parameters[3];
        $this->assertEquals('gender', $parameter->getName());

        $chain->resolve($parameter, $ruleset);

        $expected = ['in:male,female'];
        $this->assertEquals($expected, $ruleset->get('gender'));
    }

    public function testBackedEnumInUnionType(): void
    {
        $chain = new TypeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(EnumVariety::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = $parameters[2];
        $this->assertEquals('union', $parameter->getName());

        $chain->resolve($parameter, $ruleset);

        $expected = ['in:foo,bar,baz'];
        $this->assertEquals($expected, $ruleset->get('union'));
    }

    public function testEmptyBackedEnum(): void
    {
        $chain = new TypeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(DeepDeepDown::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = $parameters[2];
        $this->assertEquals('empty', $parameter->getName());

        $chain->resolve($parameter, $ruleset);

        // Não deve adicionar regra 'in' para enum vazio
        $this->assertEmpty($ruleset->get('empty'));
    }

    public function testRegularEnumSkipped(): void
    {
        $chain = new TypeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(EnumVariety::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = $parameters[0];
        $this->assertEquals('enum', $parameter->getName());

        $chain->resolve($parameter, $ruleset);

        // Não deve adicionar regra para enum sem valor
        $this->assertEmpty($ruleset->get('enum'));
    }

    public function testMixedTypeSkipped(): void
    {
        $chain = new TypeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(AttributesVariety::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = $parameters[9];
        $this->assertEquals('noAttribute', $parameter->getName());

        $chain->resolve($parameter, $ruleset);

        $this->assertEmpty($ruleset->get('noAttribute'));
    }

    public function testNonBuiltinType(): void
    {
        $chain = new TypeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(Complex::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = $parameters[0];
        $this->assertEquals('entity', $parameter->getName());

        $chain->resolve($parameter, $ruleset);

        $this->assertEmpty($ruleset->get('entity'));
    }

    public function testNullType(): void
    {
        $chain = new TypeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(Variety::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = $parameters[3];
        $this->assertEquals('whatever', $parameter->getName());
        $this->assertNull($parameter->getType());

        $chain->resolve($parameter, $ruleset);

        $this->assertEmpty($ruleset->get('whatever'));
    }
}
