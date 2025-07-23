<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Reflective\Factory;

use Constructo\Support\Reflective\Factory\Ruler;
use Constructo\Support\Reflective\Ruleset;
use Constructo\Test\Stub\Command;
use PHPUnit\Framework\TestCase;

class RulerTest extends TestCase
{
    private Ruler $ruler;

    private Ruleset $rules;

    protected function setUp(): void
    {
        $this->ruler = new Ruler();
        $this->rules = $this->ruler->ruleset(Command::class);
    }

    public function testRulesetShouldHaveExpectedCount(): void
    {
        $this->assertCount(19, $this->rules->all(), 'Ruleset should contain exactly 19 rules');
    }

    /**
     * @dataProvider requiredFieldsProvider
     */
    public function testRequiredFields(string $field): void
    {
        $this->assertContains('required', $this->rules->get($field), "Field '{$field}' should be required");
    }

    /**
     * @return array<array<string>>
     */
    public static function requiredFieldsProvider(): array
    {
        return [
            ['email'],
            ['ip_address'],
            ['signup_date'],
            ['first_name'],
            ['password'],
        ];
    }

    /**
     * @dataProvider optionalFieldsProvider
     */
    public function testOptionalFields(string $field): void
    {
        $this->assertContains('sometimes', $this->rules->get($field), "Field '{$field}' should be optional");
    }

    /**
     * @return array<array<string>>
     */
    public static function optionalFieldsProvider(): array
    {
        return [
            ['address'],
            ['city'],
            ['state'],
            ['zip'],
            ['phone'],
            ['lead_id'],
            ['birthday'],
            ['dob'],
            ['c_1'],
            ['hid'],
            ['car_make'],
            ['car_model'],
            ['car_year'],
        ];
    }

    public function testRulesetWithPath(): void
    {
        $this->ruler = new Ruler(path: ['user']);
        $nestedRules = $this->ruler->ruleset(Command::class);

        // Test path prefixing works correctly
        foreach ($nestedRules->all() as $field => $rules) {
            $this->assertStringStartsWith('user.', $field, 'Nested field should be prefixed with path');
        }
    }

    public function testRulesetCaching(): void
    {
        // Check that generating the same ruleset twice produces identical results
        $rules1 = $this->ruler->ruleset(Command::class);
        $rules2 = $this->ruler->ruleset(Command::class);

        $this->assertEquals($rules1, $rules2, 'Generated rulesets should be identical');
    }
}
