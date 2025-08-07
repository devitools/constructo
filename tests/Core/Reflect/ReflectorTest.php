<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Reflect;

use Constructo\Contract\Reflect\TypesFactory;
use Constructo\Core\Reflect\Introspection\Introspector;
use Constructo\Core\Reflect\Reflector;
use Constructo\Core\Serialize\Builder;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Factory\SchemaFactory;
use Constructo\Support\Cache;
use Constructo\Support\Metadata\Schema\Registry\Types;
use Constructo\Support\Reflective\Notation;
use Constructo\Test\Stub\Domain\Entity\Command\GameCommand;
use Constructo\Test\Stub\Domain\Entity\Command\PersonCommand;
use Constructo\Test\Stub\Domain\Entity\EmptyClass;
use Constructo\Test\Stub\Reflector\Sample;
use Constructo\Testing\MakeExtension;
use PHPUnit\Framework\TestCase;

use function json_decode;

class ReflectorTest extends TestCase
{
    use MakeExtension;

    private Reflector $reflector;

    protected function setUp(): void
    {
        $types = $this->make(Types::class);
        $cache = $this->make(Cache::class);
        $builder = $this->make(Builder::class);
        $introspector = $this->make(Introspector::class);
        $notation = Notation::SNAKE;

        $typesFactory = $this->createMock(TypesFactory::class);
        $typesFactory->method('make')
            ->willReturn($types);

        $specsData = [
            'required' => [],
            'present' => [],
            'filled' => [],
            'sometimes' => [],
            'nullable' => [],
            'string' => [],
            'integer' => [],
            'numeric' => [],
            'boolean' => [],
            'array' => [],
            'object' => [],
            'float' => [],
            'date' => [],
            'enum' => [],
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
            'bail' => [],
            'size' => ['params' => ['size']],
        ];

        $specsFactory = new DefaultSpecsFactory($builder, $specsData);
        $schemaFactory = new SchemaFactory($specsFactory);


        $this->reflector = new Reflector($schemaFactory, $types, $cache, $introspector, $notation);
    }

    public function testReflectSampleClassCreatesSchemaWithAllFields(): void
    {
        $schema = $this->reflector->reflect(Sample::class);

        $this->assertNotNull($schema);

        $this->assertNotNull($schema->get('required_field'));
        $this->assertNotNull($schema->get('required_nullable_field'));
        $this->assertNotNull($schema->get('required_enum_field'));
        $this->assertNotNull($schema->get('required_nullable_enum_field'));

        $this->assertNotNull($schema->get('processed_field'));
        $this->assertNotNull($schema->get('processed_nullable_field'));

        $this->assertNotNull($schema->get('default_string_field'));
        $this->assertNotNull($schema->get('default_null_field'));
        $this->assertNotNull($schema->get('optional_array_field'));
        $this->assertNotNull($schema->get('optional_object_field'));
        $this->assertNotNull($schema->get('default_enum_field'));
    }

    public function testReflectSampleClassFieldRequirements(): void
    {
        $schema = $this->reflector->reflect(Sample::class);

        $requiredField = $schema->get('required_field');
        $this->assertTrue($requiredField->hasRule('required'));

        $requiredNullableField = $schema->get('required_nullable_field');
        $this->assertTrue($requiredNullableField->hasRule('present'));

        $requiredEnumField = $schema->get('required_enum_field');
        $this->assertTrue($requiredEnumField->hasRule('required'));

        $requiredNullableEnumField = $schema->get('required_nullable_enum_field');
        $this->assertTrue($requiredNullableEnumField->hasRule('present'));

        $processedField = $schema->get('processed_field');
        $this->assertTrue($processedField->hasRule('required'));

        $defaultStringField = $schema->get('default_string_field');
        $this->assertTrue($defaultStringField->hasRule('sometimes'));

        $defaultNullField = $schema->get('default_null_field');
        $this->assertTrue($defaultNullField->hasRule('sometimes'));

        $optionalArrayField = $schema->get('optional_array_field');
        $this->assertTrue($optionalArrayField->hasRule('sometimes'));

        $optionalObjectField = $schema->get('optional_object_field');
        $this->assertTrue($optionalObjectField->hasRule('sometimes'));

        $defaultEnumField = $schema->get('default_enum_field');
        $this->assertTrue($defaultEnumField->hasRule('sometimes'));

        $processedNullableField = $schema->get('processed_nullable_field');
        $this->assertTrue($processedNullableField->hasRule('sometimes'));
    }

    public function testReflectSampleClassFieldTypes(): void
    {
        $schema = $this->reflector->reflect(Sample::class);

        $requiredField = $schema->get('required_field');
        $this->assertTrue($requiredField->hasRule('string'));

        $requiredNullableField = $schema->get('required_nullable_field');
        $this->assertTrue($requiredNullableField->hasRule('string'));

        $defaultStringField = $schema->get('default_string_field');
        $this->assertTrue($defaultStringField->hasRule('string'));

        $processedField = $schema->get('processed_field');
        $this->assertTrue($processedField->hasRule('string'));

        $optionalArrayField = $schema->get('optional_array_field');
        $this->assertTrue($optionalArrayField->hasRule('array'));

        // Object field should have array rule
        $optionalObjectField = $schema->get('optional_object_field');
        $this->assertTrue($optionalObjectField->hasRule('array'));
    }

    public function testReflectSampleClassFieldNullability(): void
    {
        $schema = $this->reflector->reflect(Sample::class);

        // Non-nullable fields should not have nullable rule
        $requiredField = $schema->get('required_field');
        $this->assertFalse($requiredField->hasRule('nullable'));

        $processedField = $schema->get('processed_field');
        $this->assertFalse($processedField->hasRule('nullable'));

        // Nullable fields should have nullable rule
        $requiredNullableField = $schema->get('required_nullable_field');
        $this->assertTrue($requiredNullableField->hasRule('nullable'));

        $defaultNullField = $schema->get('default_null_field');
        $this->assertTrue($defaultNullField->hasRule('nullable'));

        $optionalArrayField = $schema->get('optional_array_field');
        $this->assertTrue($optionalArrayField->hasRule('nullable'));

        $optionalObjectField = $schema->get('optional_object_field');
        $this->assertTrue($optionalObjectField->hasRule('nullable'));

        $processedNullableField = $schema->get('processed_nullable_field');
        $this->assertTrue($processedNullableField->hasRule('nullable'));
    }

    public function testReflectSampleClassEnumFields(): void
    {
        $schema = $this->reflector->reflect(Sample::class);

        // Enum fields should have proper enum type handling
        $requiredEnumField = $schema->get('required_enum_field');
        $this->assertNotNull($requiredEnumField);
        $this->assertTrue($requiredEnumField->hasRule('required'));
        $this->assertFalse($requiredEnumField->hasRule('nullable'));

        $requiredNullableEnumField = $schema->get('required_nullable_enum_field');
        $this->assertNotNull($requiredNullableEnumField);
        $this->assertTrue($requiredNullableEnumField->hasRule('present'));
        $this->assertTrue($requiredNullableEnumField->hasRule('nullable'));

        $defaultEnumField = $schema->get('default_enum_field');
        $this->assertNotNull($defaultEnumField);
        $this->assertTrue($defaultEnumField->hasRule('sometimes'));
        $this->assertFalse($defaultEnumField->hasRule('nullable'));
    }

    public function testExtractCompleteJsonSchemaStructure(): void
    {
        $schema = $this->reflector->reflect(Sample::class);
        $rules = $schema->rules();

        $json = '{
          "required_field" : [ "required", "string" ],
          "required_nullable_field" : [ "present", "nullable", "string" ],
          "required_enum_field" : [ "required", "string", "in:first,second,third" ],
          "required_nullable_enum_field" : [ "present", "nullable", "string", "in:first,second,third" ],
          "processed_field" : [ "required", "string" ],
          "default_string_field" : [ "sometimes", "required", "string" ],
          "default_null_field" : [ "sometimes", "nullable", "string" ],
          "optional_array_field" : [ "sometimes", "nullable", "array" ],
          "optional_object_field" : [ "sometimes", "nullable", "array" ],
          "optional_object_field.name" : [ "sometimes", "regex:/^[a-zA-Z]{1,255}$/", "string" ],
          "optional_object_field.slug" : [ "sometimes", "string" ],
          "optional_object_field.published_at" : [ "sometimes", "date" ],
          "optional_object_field.data" : [ "sometimes", "array" ],
          "optional_object_field.features" : [ "sometimes", "array" ],
          "optional_object_field.features.*.name" : [ "sometimes", "string" ],
          "optional_object_field.features.*.description" : [ "sometimes", "string" ],
          "optional_object_field.features.*.enabled" : [ "sometimes", "boolean" ],
          "default_enum_field" : [ "sometimes", "required", "integer", "in:1,2,3" ],
          "processed_nullable_field" : [ "sometimes", "nullable", "string" ]
        }';
        $expected = json_decode($json, true);

        $this->assertEquals($expected, $rules);
    }

    public function testReflectorReturnsCorrectRulesForGameCommand(): void
    {
        $schema = $this->reflector->reflect(GameCommand::class);
        $rules = $schema->rules();
        $json = '{
          "name": [ "required", "string" ],
          "slug": [ "required", "string" ],
          "published_at": [ "required", "date" ],
          "data": [ "required", "array" ],
          "features": [ "required", "array" ],
          "features.*.name": [ "required", "string" ],
          "features.*.description": [ "required", "string" ],
          "features.*.enabled": [ "required", "boolean" ]
        }';
        $expected = json_decode($json, true);
        $this->assertEquals($expected, $rules);
    }

    public function testReflectReturnsCorrectRulesForPersonCommand(): void
    {
        $schema = $this->reflector->reflect(PersonCommand::class);
        $rules = $schema->rules();
        $json = '{
          "name" : [ "required", "string" ],
          "mom" : [ "present", "nullable", "array" ],
          "mom.name" : [ "required", "string" ],
          "mom.mom" : [ "present", "nullable", "array" ],
          "mom.dad" : [ "sometimes", "nullable", "array" ],
          "dad" : [ "sometimes", "nullable", "array" ],
          "dad.name" : [ "sometimes", "string" ],
          "dad.mom" : [ "sometimes", "nullable", "array" ],
          "dad.dad" : [ "sometimes", "nullable", "array" ],
          "external_id" : [ "sometimes", "required" ]
        }';
        $expected = json_decode($json, true);
        $this->assertEquals($expected, $rules);
    }

    public function testReflectClassWithEmptySourceReturnsEarly(): void
    {
        // Create a stub class that has EmptyClass as a field source
        $stubClass = new class(new EmptyClass()) {
            public function __construct(
                public EmptyClass $emptyField,
            ) {
            }
        };

        $schema = $this->reflector->reflect($stubClass::class);
        $rules = $schema->rules();

        // Should have the field but no nested rules since EmptyClass has no parameters
        $this->assertArrayHasKey('empty_field', $rules);
        $this->assertEquals(
            [
                'required',
                'array',
            ],
            $rules['empty_field']
        );

        // Should not have any nested rules for empty_field since EmptyClass has no constructor parameters
        $nestedKeys = array_filter(array_keys($rules), fn ($key) => str_starts_with($key, 'empty_field.'));
        $this->assertEmpty($nestedKeys);
    }
}
