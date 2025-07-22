<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Deserialize\Resolve;

use Constructo\Core\Deserialize\Resolve\DoNothingChain;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

final class DoNothingChainTest extends TestCase
{
    public function testResolveValue(): void
    {
        $chain = new DoNothingChain();
        $value = 'test';
        $result = $chain->resolve($this->createMock(ReflectionParameter::class), $value);

        $this->assertEquals('test', $result->content);
    }
}
