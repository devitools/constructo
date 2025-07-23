<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Reflective;

use Constructo\Support\Reflective\Ruleset;
use PHPUnit\Framework\TestCase;
use Stringable;

class RulesetTest extends TestCase
{
    public function testShouldAddRuleWithoutParameters(): void
    {
        $ruleset = new Ruleset();
        $result = $ruleset->add('email', 'required');

        $this->assertTrue($result);
        $this->assertContains('required', $ruleset->get('email'));
    }

    public function testShouldAddRuleWithParameters(): void
    {
        $ruleset = new Ruleset();
        $result = $ruleset->add('age', 'between', 18, 65);

        $this->assertTrue($result);
        $this->assertContains('between:18,65', $ruleset->get('age'));
    }

    public function testShouldReturnFalseWhenAddingInvalidRule(): void
    {
        $ruleset = new Ruleset();
        $result = $ruleset->add('field', 'invalid_rule');

        $this->assertFalse($result);
        $this->assertEmpty($ruleset->get('field'));
    }

    public function testShouldAddMultipleRulesToSameField(): void
    {
        $ruleset = new Ruleset();
        $ruleset->add('email', 'required');
        $ruleset->add('email', 'email');

        $rules = $ruleset->get('email');

        $this->assertCount(2, $rules);
        $this->assertContains('required', $rules);
        $this->assertContains('email', $rules);
    }

    public function testShouldReturnEmptyArrayForNonexistentField(): void
    {
        $ruleset = new Ruleset();

        $this->assertEmpty($ruleset->get('nonexistent'));
    }

    public function testShouldReturnAllRules(): void
    {
        $ruleset = new Ruleset();
        $ruleset->add('email', 'required');
        $ruleset->add('email', 'email');
        $ruleset->add('age', 'numeric');
        $ruleset->add('age', 'min', 18);

        $allRules = $ruleset->all();

        $this->assertCount(2, $allRules);
        $this->assertArrayHasKey('email', $allRules);
        $this->assertArrayHasKey('age', $allRules);
        $this->assertCount(2, $allRules['email']);
        $this->assertCount(2, $allRules['age']);
    }

    public function testShouldHandleStringableObjects(): void
    {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'required';
            }
        };

        $ruleset = new Ruleset();
        $result = $ruleset->add('field', $stringable);

        $this->assertTrue($result);
        $this->assertContains('required', $ruleset->get('field'));
    }

    public function testShouldHandleStringableParametersObjects(): void
    {
        $stringableParam = new class implements Stringable {
            public function __toString(): string
            {
                return 'param';
            }
        };

        $ruleset = new Ruleset();
        $result = $ruleset->add('field', 'same', $stringableParam);

        $this->assertTrue($result);
        $this->assertContains('same:param', $ruleset->get('field'));
    }
}
