<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Metadata\Schema;

use BadMethodCallException;
use Constructo\Core\Metadata\Schema\Field;
use Constructo\Core\Metadata\Schema\Field\Rules;
use Constructo\Core\Metadata\Schema\Registry\Spec;
use Constructo\Core\Metadata\Schema\Registry\Specs;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Support\Set;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FieldTest extends TestCase
{
    private Specs $specs;

    protected function setUp(): void
    {
        $specsData = [
            'required' => [],
            'string' => [],
            'integer' => [],
            'numeric' => [],
            'boolean' => [],
            'email' => [],
            'url' => [],
            'uuid' => [],
            'min' => ['params' => ['min']],
            'max' => ['params' => ['max']],
            'between' => ['params' => ['min', 'max']],
            'in' => ['params' => ['values']],
            'regex' => ['params' => ['pattern', 'parameters:optional']],
            'nullable' => [],
            'sometimes' => [],
            'bail' => [],
        ];

        $specsFactory = new DefaultSpecsFactory($specsData);
        $this->specs = $specsFactory->make();
    }

    public function testCanCreateFieldWithBasicProperties(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $this->assertSame('test_field', $field->name);
        $this->assertTrue($field->isAvailable());
        $this->assertNull($field->mapping());
        $this->assertNull($field->getSource());
    }

    public function testCanSetAndGetSource(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $field->setSource('original_field');

        $this->assertSame('original_field', $field->getSource());
    }

    public function testCanCallRequiredRule(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $result = $field->required();

        $this->assertSame($field, $result);
        $this->assertTrue($field->hasRule('required'));
        $this->assertContains('required', $field->rules());
    }

    public function testCanCallStringRule(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $result = $field->string();

        $this->assertSame($field, $result);
        $this->assertTrue($field->hasRule('string'));
        $this->assertContains('string', $field->rules());
    }

    public function testCanCallRuleWithParameters(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $result = $field->min(5);

        $this->assertSame($field, $result);
        $this->assertTrue($field->hasRule('min'));
        $this->assertContains('min:5', $field->rules());
    }

    public function testCanCallRuleWithMultipleParameters(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $result = $field->between(5, 10);

        $this->assertSame($field, $result);
        $this->assertTrue($field->hasRule('between'));
        $this->assertContains('between:5,10', $field->rules());
    }

    public function testCanChainMultipleRules(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $result = $field->required()->string()->min(3)->max(50);

        $this->assertSame($field, $result);
        $this->assertTrue($field->hasRule('required'));
        $this->assertTrue($field->hasRule('string'));
        $this->assertTrue($field->hasRule('min'));
        $this->assertTrue($field->hasRule('max'));

        $rules = $field->rules();
        $this->assertContains('required', $rules);
        $this->assertContains('string', $rules);
        $this->assertContains('min:3', $rules);
        $this->assertContains('max:50', $rules);
    }

    public function testCanSetMappingWithString(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $result = $field->map('mapped_field');

        $this->assertSame($field, $result);
        $this->assertSame('mapped_field', $field->mapping());
    }

    public function testCanSetMappingWithClosure(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');
        $closure = fn($value) => strtoupper($value);

        $result = $field->map($closure);

        $this->assertSame($field, $result);
        $this->assertSame($closure, $field->mapping());
    }

    public function testCanSetFieldAsUnavailable(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $result = $field->unavailable();

        $this->assertSame($field, $result);
        $this->assertFalse($field->isAvailable());
    }

    public function testCanSetFieldAsAvailable(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        // First make it unavailable
        $field->unavailable();
        $this->assertFalse($field->isAvailable());

        // Then make it available again
        $result = $field->available();

        $this->assertSame($field, $result);
        $this->assertTrue($field->isAvailable());
    }

    public function testHasRuleReturnsFalseForNonExistentRule(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $this->assertFalse($field->hasRule('nonexistent'));
    }

    public function testRulesReturnsEmptyArrayInitially(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $this->assertSame([], $field->rules());
    }

    public function testThrowsExceptionForUnsupportedMethod(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage("Entry 'unsupported' is not supported.");

        $field->unsupported();
    }

    public function testCanCallAllBasicValidationRules(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $field->required()
            ->string()
            ->integer()
            ->numeric()
            ->boolean()
            ->email()
            ->url()
            ->uuid()
            ->nullable()
            ->sometimes()
            ->bail();

        $this->assertTrue($field->hasRule('required'));
        $this->assertTrue($field->hasRule('string'));
        $this->assertTrue($field->hasRule('integer'));
        $this->assertTrue($field->hasRule('numeric'));
        $this->assertTrue($field->hasRule('boolean'));
        $this->assertTrue($field->hasRule('email'));
        $this->assertTrue($field->hasRule('url'));
        $this->assertTrue($field->hasRule('uuid'));
        $this->assertTrue($field->hasRule('nullable'));
        $this->assertTrue($field->hasRule('sometimes'));
        $this->assertTrue($field->hasRule('bail'));
    }

    public function testCanCallRulesWithArrayParameters(): void
    {
        $field = new Field($this->specs, new Rules(), 'test_field');

        $result = $field->in(['option1', 'option2', 'option3']);

        $this->assertSame($field, $result);
        $this->assertTrue($field->hasRule('in'));
        $this->assertContains('in:option1,option2,option3', $field->rules());
    }

    public function testFieldConstantsAreCorrect(): void
    {
        $this->assertSame(['map'], Field::MAPPING);
        $this->assertSame(['unavailable', 'available'], Field::VISIBILITY);
    }

    public function testComplexScenarioWithMultipleOperations(): void
    {
        $field = new Field($this->specs, new Rules(), 'user_email');

        $field->required()
            ->string()
            ->email()
            ->max(255)
            ->map('email_address')
            ->setSource('user_email_field');

        $this->assertSame('user_email', $field->name);
        $this->assertTrue($field->isAvailable());
        $this->assertSame('email_address', $field->mapping());
        $this->assertSame('user_email_field', $field->getSource());

        $rules = $field->rules();
        $this->assertContains('required', $rules);
        $this->assertContains('string', $rules);
        $this->assertContains('email', $rules);
        $this->assertContains('max:255', $rules);

        $this->assertTrue($field->hasRule('required'));
        $this->assertTrue($field->hasRule('string'));
        $this->assertTrue($field->hasRule('email'));
        $this->assertTrue($field->hasRule('max'));
    }
}
