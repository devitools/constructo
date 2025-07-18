<?php

declare(strict_types=1);

namespace Morph\Test\Core\Deserialize\Resolve;

use Morph\Contract\Formatter;
use Morph\Core\Deserialize\Resolve\FormatterChain;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use stdClass;

use function Morph\Json\encode;

final class FormatterChainTest extends TestCase
{
    #[TestWith([10.5])]
    #[TestWith([true])]
    #[TestWith([null])]
    #[TestWith([new stdClass()])]
    public function testResolveWithoutConverter(mixed $value): void
    {
        $chain = new FormatterChain();
        $result = $chain->resolve($this->createMock(ReflectionParameter::class), $value);

        $this->assertSame($value, $result->content);
    }

    public function testResolveWithArrayValue(): void
    {
        $converter = new class implements Formatter {
            public function format(mixed $value, mixed $option = null): ?string
            {
                return encode($value);
            }
        };
        $chain = new FormatterChain(formatters: ['array' => $converter]);
        $value = ['key' => 'value'];
        $result = $chain->resolve($this->createMock(ReflectionParameter::class), $value);

        $this->assertEquals('{"key":"value"}', $result->content);
    }
}
