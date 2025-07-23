<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Reflective\Factory\Ruler;

use Constructo\Support\Reflective\Factory\Chain;
use Constructo\Support\Reflective\Factory\Ruler\RecursiveChain;
use Constructo\Support\Reflective\Ruleset;
use Constructo\Test\Stub\Command;
use Constructo\Test\Stub\Complex;
use Constructo\Test\Stub\Deep;
use Constructo\Test\Stub\Union;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RecursiveChainTest extends TestCase
{
    private RecursiveChain $chain;

    private Ruleset $ruleset;

    protected function setUp(): void
    {
        $this->chain = new RecursiveChain();
        $this->ruleset = new Ruleset();
    }

    public function testDoesNotProcessPrimitiveTypes(): void
    {
        $reflection = new ReflectionClass(Command::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Find a primitive type parameter (like email)
        $primitiveParam = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'email') {
                $primitiveParam = $param;
                break;
            }
        }

        $this->assertNotNull($primitiveParam, 'Test stub missing expected parameter');
        $this->chain->resolve($primitiveParam, $this->ruleset);

        // Should not add nested rules for primitives
        $this->assertCount(0, $this->ruleset->all());
    }

    public function testProcessesObjectTypes(): void
    {
        $reflection = new ReflectionClass(Complex::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Find parameter with object type
        $objectParam = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'entity') {
                $objectParam = $param;
                break;
            }
        }

        $this->assertNotNull($objectParam, 'Test stub missing expected parameter');

        $this->chain = new RecursiveChain(path: ['complex']);
        $this->chain->resolve($objectParam, $this->ruleset);

        // Should recursively process the object type
        $this->assertNotEmpty($this->ruleset->all());
        $this->assertArrayHasKey('complex.entity.id', $this->ruleset->all());
    }

    public function testHandlesUnionTypes(): void
    {
        $reflection = new ReflectionClass(Union::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Find parameter with union type containing an object
        $unionParam = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'more') {
                $unionParam = $param;
                break;
            }
        }

        $this->assertNotNull($unionParam, 'Test stub missing expected parameter');

        $this->chain = new RecursiveChain(path: ['more']);
        $this->chain->resolve($unionParam, $this->ruleset);

        // Should process the object type within the union
        $this->assertNotEmpty($this->ruleset->all());
    }

    public function testNestedPathPrefixing(): void
    {
        $reflection = new ReflectionClass(Complex::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Find parameter with object type
        $objectParam = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'entity') {
                $objectParam = $param;
                break;
            }
        }

        $this->assertNotNull($objectParam, 'Test stub missing expected parameter');

        $this->chain = new RecursiveChain(path: ['parent', 'entity']);
        $this->chain->resolve($objectParam, $this->ruleset);

        // Check that fields are properly prefixed
        foreach ($this->ruleset->all() as $field => $rules) {
            $this->assertStringStartsWith('parent.entity.', $field);
        }
    }

    public function testDeepNestedObjects(): void
    {
        $reflection = new ReflectionClass(Deep::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Find parameter for deepDown
        $deepParam = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'deepDown') {
                $deepParam = $param;
                break;
            }
        }

        $this->assertNotNull($deepParam, 'Test stub missing expected parameter');

        $this->chain->resolve($deepParam, $this->ruleset);

        // Should process deeply nested objects
        $this->assertNotEmpty($this->ruleset->all());
        $this->assertArrayHasKey('deep_down.deep_deep_down.stub.id', $this->ruleset->all());
    }

    public function testChainingWithOtherResolvers(): void
    {
        $reflection = new ReflectionClass(Complex::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $objectParam = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'entity') {
                $objectParam = $param;
                break;
            }
        }

        $this->assertNotNull($objectParam, 'Test stub missing expected parameter');

        $nextChain = $this->createMock(Chain::class);
        $nextChain->expects($this->once())
            ->method('resolve')
            ->willReturn($this->ruleset);

        $nextChain->then($this->chain)
            ->resolve($objectParam, $this->ruleset);
    }
}
