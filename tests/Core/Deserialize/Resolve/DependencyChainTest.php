<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Deserialize\Resolve;

use Constructo\Core\Deserialize\Resolve\DependencyChain;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use stdClass;

final class DependencyChainTest extends TestCase
{
    public function testResolveObject(): void
    {
        $chain = new DependencyChain();
        $object = new stdClass();
        $result = $chain->resolve($this->createMock(ReflectionParameter::class), $object);

        $this->assertIsArray($result->content);
    }

    public function testResolveNonObject(): void
    {
        $chain = new DependencyChain();
        $value = 'test';
        $result = $chain->resolve($this->createMock(ReflectionParameter::class), $value);

        $this->assertEquals('test', $result->content);
    }

    public function testResolveBackedEnum(): void
    {
        $chain = new DependencyChain();
        $enum = TestStatus::ACTIVE;
        $result = $chain->resolve($this->createMock(ReflectionParameter::class), $enum);

        $this->assertEquals('active', $result->content);
    }
}

enum TestStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
