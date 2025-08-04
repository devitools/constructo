<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Metadata\Schema\Field;

use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Spec;
use Constructo\Support\Set;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RulesTest extends TestCase
{
    private Rules $rules;

    protected function setUp(): void
    {
        $this->rules = new Rules();
    }

    public function testCanRegisterRuleWithoutArguments(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('required', $properties, null);

        $this->rules->register($spec, []);

        $this->assertTrue($this->rules->has('required'));
    }

    public function testCanRegisterRuleWithArguments(): void
    {
        $properties = Set::createFrom(['params' => ['min']]);
        $spec = new Spec('min', $properties, null);

        $this->rules->register($spec, [10]);

        $this->assertTrue($this->rules->has('min'));
    }

    public function testCanRegisterRuleWithKindProperty(): void
    {
        $properties = Set::createFrom(['kind' => 'type']);
        $spec = new Spec('string', $properties, null);

        $this->rules->register($spec, []);

        $this->assertTrue($this->rules->has('type'));
        $this->assertFalse($this->rules->has('string'));
    }

    public function testHasReturnsFalseForNonExistentRule(): void
    {
        $this->assertFalse($this->rules->has('nonexistent'));
    }

    public function testAllReturnsEmptyArrayWhenNoRules(): void
    {
        $result = $this->rules->all();

        $this->assertSame([], $result);
    }

    public function testAllReturnsSingleRuleAsString(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('required', $properties, null);
        $this->rules->register($spec, []);

        $result = $this->rules->all();

        $this->assertSame(['required'], $result);
    }

    public function testAllReturnsMultipleRulesAsStrings(): void
    {
        $requiredProperties = Set::createFrom([]);
        $requiredSpec = new Spec('required', $requiredProperties, null);
        $this->rules->register($requiredSpec, []);

        $minProperties = Set::createFrom(['params' => ['min']]);
        $minSpec = new Spec('min', $minProperties, null);
        $this->rules->register($minSpec, [10]);

        $result = $this->rules->all();

        $this->assertCount(2, $result);
        $this->assertContains('required', $result);
        $this->assertContains('min:10', $result);
    }

    public function testRegisteringRuleWithSameKeyOverwritesPrevious(): void
    {
        $properties = Set::createFrom(['params' => ['min']]);
        $spec = new Spec('min', $properties, null);

        $this->rules->register($spec, [5]);
        $this->rules->register($spec, [10]);

        $result = $this->rules->all();

        $this->assertCount(1, $result);
        $this->assertContains('min:10', $result);
        $this->assertNotContains('min:5', $result);
    }

    public function testValidationPassesWhenNoParamsRequired(): void
    {
        $properties = Set::createFrom([]);
        $spec = new Spec('required', $properties, null);

        // Should not throw exception
        $this->rules->register($spec, []);

        $this->assertTrue($this->rules->has('required'));
    }

    public function testValidationPassesWhenParamsIsNotArray(): void
    {
        $properties = Set::createFrom(['params' => 'not_array']);
        $spec = new Spec('custom', $properties, null);

        // Should not throw exception
        $this->rules->register($spec, []);

        $this->assertTrue($this->rules->has('custom'));
    }

    public function testValidationPassesWhenSufficientArgumentsProvided(): void
    {
        $properties = Set::createFrom(['params' => ['min', 'max']]);
        $spec = new Spec('between', $properties, null);

        // Should not throw exception
        $this->rules->register($spec, [10, 20]);

        $this->assertTrue($this->rules->has('between'));
    }

    public function testValidationPassesWhenMoreArgumentsThanRequired(): void
    {
        $properties = Set::createFrom(['params' => ['min']]);
        $spec = new Spec('min', $properties, null);

        // Should not throw exception
        $this->rules->register($spec, [10, 'extra']);

        $this->assertTrue($this->rules->has('min'));
    }

    public function testValidationThrowsExceptionWhenInsufficientArguments(): void
    {
        $properties = Set::createFrom(['params' => ['min', 'max']]);
        $spec = new Spec('between', $properties, null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec rule between expects 2 (min, max) parameters, 1 given.');

        $this->rules->register($spec, [10]);
    }

    public function testValidationIgnoresOptionalParameters(): void
    {
        $properties = Set::createFrom(['params' => ['pattern', 'parameters:optional']]);
        $spec = new Spec('regex', $properties, null);

        // Should not throw exception - only 'pattern' is required
        $this->rules->register($spec, ['/^test$/']);

        $this->assertTrue($this->rules->has('regex'));
    }

    public function testValidationWithOnlyOptionalParameters(): void
    {
        $properties = Set::createFrom(['params' => ['param1:optional', 'param2:optional']]);
        $spec = new Spec('optional_rule', $properties, null);

        // Should not throw exception - no required parameters
        $this->rules->register($spec, []);

        $this->assertTrue($this->rules->has('optional_rule'));
    }

    public function testValidationWithMixedRequiredAndOptionalParameters(): void
    {
        $properties = Set::createFrom(['params' => ['required1', 'optional1:optional', 'required2']]);
        $spec = new Spec('mixed', $properties, null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec rule mixed expects 3 (required1, required2) parameters, 1 given.');

        $this->rules->register($spec, ['value1']);
    }

    public function testValidationExceptionMessageFormat(): void
    {
        $properties = Set::createFrom(['params' => ['table', 'column', 'except']]);
        $spec = new Spec('unique', $properties, null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Spec rule unique expects 3 (table, column, except) parameters, 0 given.');

        $this->rules->register($spec, []);
    }

    public function testAllReturnsRulesInRegistrationOrder(): void
    {
        $spec1 = new Spec('required', Set::createFrom([]), null);
        $spec2 = new Spec('string', Set::createFrom(['kind' => 'type']), null);
        $spec3 = new Spec('min', Set::createFrom(['params' => ['min']]), null);

        $this->rules->register($spec1, []);
        $this->rules->register($spec2, []);
        $this->rules->register($spec3, [5]);

        $result = $this->rules->all();

        $this->assertSame(['required', 'string', 'min:5'], $result);
    }
}
