<?php

declare(strict_types=1);

namespace Constructo\Test\Support;

use Constructo\Support\Value;
use PHPUnit\Framework\TestCase;

final class ValueTest extends TestCase
{
    public function testShouldHaveContent(): void
    {
        $value = new Value('foo');
        $this->assertEquals('foo', $value->content);
    }
}
