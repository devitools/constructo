<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Reflective\Factory\Ruler;

use Constructo\Support\Reflective\Factory\Ruler\AttributeChain;
use Constructo\Support\Reflective\Ruleset;
use Constructo\Test\Stub\AttributesVariety;
use Constructo\Test\Stub\PatternMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AttributeChainTest extends TestCase
{
    public function testPatternAttributeProcessing(): void
    {
        $chain = new AttributeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(PatternMock::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = $parameters[1];
        $this->assertEquals('name', $parameter->getName());

        $chain->resolve($parameter, $ruleset);

        $this->assertEquals(['regex:/^[a-zA-Z]{1,255}$/'], $ruleset->get('name'));
    }

    public function testPatternAttributeWithNumericType(): void
    {
        $chain = new AttributeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(PatternMock::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = $parameters[0];
        $this->assertEquals('id', $parameter->getName());

        $chain->resolve($parameter, $ruleset);

        $this->assertEquals(['regex:/^[0-9]{1,20}$/'], $ruleset->get('id'));
    }

    public function testPatternAttributeWithUnionType(): void
    {
        $chain = new AttributeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(PatternMock::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = $parameters[2];
        $this->assertEquals('code', $parameter->getName());

        $chain->resolve($parameter, $ruleset);

        $this->assertEquals(['regex:/^[0-9]{1,20}$/'], $ruleset->get('code'));
    }

    public function testDefineAttributeProcessing(): void
    {
        $chain = new AttributeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(AttributesVariety::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'email') {
                $parameter = $param;
                break;
            }
        }

        $this->assertNotNull($parameter);

        $chain->resolve($parameter, $ruleset);

        $this->assertEquals(['email'], $ruleset->get('email'));
    }

    public function testDefineAttributeWithTypeExtended(): void
    {
        $chain = new AttributeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(AttributesVariety::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'sensitive') {
                $parameter = $param;
                break;
            }
        }

        $this->assertNotNull($parameter);

        $chain->resolve($parameter, $ruleset);

        $this->assertEmpty($ruleset->get('sensitive'));
    }

    public function testParameterWithoutAttributes(): void
    {
        $chain = new AttributeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(AttributesVariety::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'noAttribute') {
                $parameter = $param;
                break;
            }
        }

        $this->assertNotNull($parameter);

        $chain->resolve($parameter, $ruleset);

        $this->assertEmpty($ruleset->get('noAttribute'));
    }

    public function testUnsupportedAttribute(): void
    {
        $chain = new AttributeChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(AttributesVariety::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $parameter = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'cpf') {
                $parameter = $param;
                break;
            }
        }

        $this->assertNotNull($parameter);

        $chain->resolve($parameter, $ruleset);

        $this->assertEmpty($ruleset->get('cpf'));
    }
}
