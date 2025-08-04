<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Metadata\Schema\Field;

use Constructo\Contract\Formatter;
use Constructo\Core\Metadata\Schema\Field\Rule;
use Constructo\Core\Metadata\Schema\Registry\Spec;
use Constructo\Support\Set;
use PHPUnit\Framework\TestCase;

final class RuleTest extends TestCase
{
    public function testRuleConstructionWithBasicSpec(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('required', $properties, null);

        $rule = new Rule($spec, []);

        $this->assertSame($spec, $rule->spec);
        $this->assertSame([], $rule->arguments);
        $this->assertSame('required', $rule->key);
    }

    public function testRuleConstructionWithKindProperty(): void
    {
        $properties = Set::createFrom(['kind' => 'type']);
        $spec = new Spec('string', $properties, null);

        $rule = new Rule($spec, []);

        $this->assertSame('type', $rule->key);
    }

    public function testRuleConstructionWithArguments(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('min', $properties, null);
        $arguments = ['value' => 10];

        $rule = new Rule($spec, $arguments);

        $this->assertSame($arguments, $rule->arguments);
    }

    public function testToStringWithoutArguments(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('required', $properties, null);
        $rule = new Rule($spec, []);

        $result = $rule->__toString();

        $this->assertSame('required', $result);
    }

    public function testToStringWithSimpleArguments(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('min', $properties, null);
        $rule = new Rule($spec, [10]);

        $result = $rule->__toString();

        $this->assertSame('min:10', $result);
    }

    public function testToStringWithMultipleArguments(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('between', $properties, null);
        $rule = new Rule($spec, [10, 20]);

        $result = $rule->__toString();

        $this->assertSame('between:10,20', $result);
    }

    public function testToStringWithStringArguments(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('regex', $properties, null);
        $rule = new Rule($spec, ['/^[a-z]+$/']);

        $result = $rule->__toString();

        $this->assertSame('regex:/^[a-z]+$/', $result);
    }

    public function testToStringWithArrayArguments(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('in', $properties, null);
        $rule = new Rule($spec, [['apple', 'banana', 'cherry']]);

        $result = $rule->__toString();

        $this->assertSame('in:apple,banana,cherry', $result);
    }

    public function testToStringWithNestedArrayArguments(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('complex', $properties, null);
        $rule = new Rule($spec, [['a', ['b', 'c']], 'd']);

        $result = $rule->__toString();

        $this->assertSame('complex:a,b,c,d', $result);
    }

    public function testToStringWithMixedArguments(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('mixed', $properties, null);
        $rule = new Rule($spec, ['string', 42, true, ['array', 'values']]);

        $result = $rule->__toString();

        $this->assertSame('mixed:string,42,1,array,values', $result);
    }

    public function testToStringWithFormatter(): void
    {
        $formatter = $this->createMock(Formatter::class);
        $formatter->expects($this->once())
            ->method('format')
            ->with(['value1', 'value2'])
            ->willReturn(['formatted1', 'formatted2']);

        $properties = Set::createFrom([]);
        $spec = new Spec('custom', $properties, $formatter);
        $rule = new Rule($spec, ['value1', 'value2']);

        $result = $rule->__toString();

        $this->assertSame('custom:formatted1,formatted2', $result);
    }

    public function testJsonSerialize(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('required', $properties, null);
        $rule = new Rule($spec, []);

        $result = $rule->jsonSerialize();

        $this->assertSame('required', $result);
    }

    public function testJsonSerializeWithArguments(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('min', $properties, null);
        $rule = new Rule($spec, [10]);

        $result = $rule->jsonSerialize();

        $this->assertSame('min:10', $result);
    }

    public function testJsonEncoding(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('between', $properties, null);
        $rule = new Rule($spec, [5, 15]);

        $json = json_encode($rule);

        $this->assertSame('"between:5,15"', $json);
    }

    public function testRuleKeyFallsBackToSpecName(): void
    {
        $properties = Set::createFrom(['other' => 'value']);
        $spec = new Spec('email', $properties, null);
        $rule = new Rule($spec, []);

        $this->assertSame('email', $rule->key);
    }

    public function testEnforceMethodWithBooleanValues(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('test', $properties, null);
        $rule = new Rule($spec, [true, false]);

        $result = $rule->__toString();

        $this->assertSame('test:1,', $result);
    }

    public function testEnforceMethodWithNullValue(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('test', $properties, null);
        $rule = new Rule($spec, [null]);

        $result = $rule->__toString();

        $this->assertSame('test:', $result);
    }
}
