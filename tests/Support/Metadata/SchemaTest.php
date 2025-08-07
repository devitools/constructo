<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Metadata;

use Constructo\Core\Serialize\Builder;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Support\Metadata\Schema;
use Constructo\Support\Metadata\Schema\Field\Fieldset;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use Constructo\Testing\MakeExtension;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    use MakeExtension;

    private Schema $schema;
    private Specs $specs;

    protected function setUp(): void
    {
        $builder = $this->make(Builder::class);
        $specs = [
            'required' => [],
            'string' => [],
            'array' => [],
            'integer' => [],
            'numeric' => [],
            'boolean' => [],
            'email' => [],
            'url' => [],
            'uuid' => [],
            'min' => ['params' => ['min']],
            'max' => ['params' => ['max']],
            'between' => [
                'params' => [
                    'min',
                    'max',
                ],
            ],
            'in' => ['params' => ['values']],
            'regex' => [
                'params' => [
                    'pattern',
                    'parameters:optional',
                ],
            ],
            'distinct' => [],
            'nullable' => [],
            'sometimes' => [],
            'bail' => [],
        ];

        $specsFactory = new DefaultSpecsFactory($builder, $specs);
        $this->specs = $specsFactory->make();
        $this->schema = new Schema($this->specs, new Fieldset());
    }

    public function testCanAddFieldToSchema(): void
    {
        $field = $this->schema->add('name');

        $this->assertSame('name', $field->name);
        $this->assertTrue($field->isAvailable());
    }

    public function testCanGetExistingField(): void
    {
        $originalField = $this->schema->add('email');
        $retrievedField = $this->schema->get('email');

        $this->assertSame($originalField, $retrievedField);
    }

    public function testGetNonExistentFieldThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Field 'nonexistent' does not exist in the schema.");

        $this->schema->get('nonexistent');
    }

    public function testFluentApiBasicValidation(): void
    {
        $field = $this->schema->add('name')
            ->required()
            ->string();

        $rules = $field->rules();

        $this->assertCount(2, $rules);
        $this->assertContains('required', $rules);
        $this->assertContains('string', $rules);
    }

    public function testFluentApiWithParameters(): void
    {
        $field = $this->schema->add('age')
            ->required()
            ->integer()
            ->min(18)
            ->max(100);

        $rules = $field->rules();

        $this->assertCount(4, $rules);
        $this->assertContains('required', $rules);
        $this->assertContains('integer', $rules);
        $this->assertContains('min:18', $rules);
        $this->assertContains('max:100', $rules);
    }

    public function testSchemaRulesMethod(): void
    {
        $this->schema->add('name')
            ->required()
            ->string();

        $this->schema->add('age')
            ->required()
            ->integer()
            ->min(18);

        $rules = $this->schema->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('age', $rules);
        $this->assertContains('required', $rules['name']);
        $this->assertContains('string', $rules['name']);
        $this->assertContains('required', $rules['age']);
        $this->assertContains('integer', $rules['age']);
        $this->assertContains('min:18', $rules['age']);
    }

    public function testFieldMapping(): void
    {
        $field = $this->schema->add('user_name')
            ->required()
            ->string()
            ->map('username');

        $mapping = $field->mapping();

        $this->assertSame('username', $mapping);
    }

    public function testSchemaMappingsMethod(): void
    {
        $this->schema->add('user_name')
            ->required()
            ->string()
            ->map('username');

        $this->schema->add('email_address')
            ->required()
            ->email()
            ->map('email');

        $this->schema->add('age')
            ->required()
            ->integer();

        $mappings = $this->schema->mappings();

        $this->assertArrayHasKey('user_name', $mappings);
        $this->assertArrayHasKey('email_address', $mappings);
        $this->assertArrayNotHasKey('age', $mappings); // No mapping defined
        $this->assertSame('username', $mappings['user_name']);
        $this->assertSame('email', $mappings['email_address']);
    }

    public function testFieldVisibility(): void
    {
        $this->schema->add('visible_field')
            ->required()
            ->string();

        $this->schema->add('hidden_field')
            ->required()
            ->string()
            ->unavailable();

        $rules = $this->schema->rules();

        $this->assertArrayHasKey('visible_field', $rules);
        $this->assertArrayNotHasKey('hidden_field', $rules);
    }

    public function testExampleUsageScenario(): void
    {
        $this->specs->register('in', ['params' => ['values']]);

        $this->schema->add('type')
            ->required()
            ->string()
            ->in(
                [
                    'Type1',
                    'Type2',
                    'Type3',
                ]
            );

        $this->schema->add('people')
            ->required()
            ->array()
            ->min(1);

        $this->schema->add('people.*')
            ->required()
            ->string()
            ->distinct()
            ->regex('/^%s(?:,%s%s%s)?$/', function () {
                $email = '[^,@]+@[^,@]+\.[^,@]+';
                $name = '[^,]*';
                $optional = sprintf('(?:,%s)?', $email);
                return [
                    $email,
                    $name,
                    $optional,
                    $optional,
                ];
            });

        $rules = $this->schema->rules();

        $this->assertArrayHasKey('type', $rules);
        $this->assertArrayHasKey('people', $rules);
        $this->assertArrayHasKey('people.*', $rules);

        $this->assertContains('required', $rules['type']);
        $this->assertContains('string', $rules['type']);
        $this->assertContains('in:Type1,Type2,Type3', $rules['type']);

        $this->assertContains('required', $rules['people']);
        $this->assertContains('array', $rules['people']);
        $this->assertContains('min:1', $rules['people']);

        $this->assertContains('required', $rules['people.*']);
        $this->assertContains('string', $rules['people.*']);
        $this->assertContains('distinct', $rules['people.*']);

        $regexRuleFound = false;
        foreach ($rules['people.*'] as $rule) {
            if (str_contains((string) $rule, 'regex:')) {
                $regexRuleFound = true;
                break;
            }
        }
        $this->assertTrue($regexRuleFound, 'Regex rule should be present');
    }

    public function testAddingFieldTwiceReturnsSameInstance(): void
    {
        $field1 = $this->schema->add('duplicate');
        $field2 = $this->schema->add('duplicate');

        $this->assertSame($field1, $field2);
    }

    public function testFieldHasRule(): void
    {
        $field = $this->schema->add('test')
            ->required()
            ->string();

        $this->assertTrue($field->hasRule('required'));
        $this->assertTrue($field->hasRule('string'));
        $this->assertFalse($field->hasRule('integer'));
    }
}
