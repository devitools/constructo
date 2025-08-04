<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Reflective;

use Constructo\Test\Support\Reflective\Engine\TestableEngine;
use Constructo\Test\Support\Reflective\Engine\TestFormatter;
use Constructo\Test\Support\Reflective\Engine\TestFormatterSubclass;
use PHPUnit\Framework\TestCase;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

final class EngineTest extends TestCase
{
    public function testSelectFormatterWithMatchingSubclass(): void
    {
        $engine = new TestableEngine([TestFormatter::class => new TestFormatter()]);

        $formatter = $engine->testSelectFormatter(TestFormatterSubclass::class);

        $this->assertIsCallable($formatter);
    }

    public function testDetectCollectionNameWithNonNamedType(): void
    {
        $engine = new TestableEngine();
        $unionType = $this->createMock(ReflectionUnionType::class);
        $parameter = $this->createMock(ReflectionParameter::class);
        $parameter->expects($this->once())
            ->method('getType')
            ->willReturn($unionType);

        $result = $engine->testDetectCollectionName($parameter);

        $this->assertNull($result);
    }

    public function testFormatTypeNameWithIntersectionType(): void
    {
        $engine = new TestableEngine();
        $namedType1 = $this->createMock(ReflectionNamedType::class);
        $namedType1->method('getName')->willReturn('TypeA');
        $namedType2 = $this->createMock(ReflectionNamedType::class);
        $namedType2->method('getName')->willReturn('TypeB');

        $intersectionType = $this->createMock(\ReflectionIntersectionType::class);
        $intersectionType->method('getTypes')->willReturn([$namedType1, $namedType2]);

        $result = $engine->testFormatTypeName($intersectionType);

        $this->assertEquals('TypeA&TypeB', $result);
    }
}
