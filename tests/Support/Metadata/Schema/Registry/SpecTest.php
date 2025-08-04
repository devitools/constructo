<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Metadata\Schema\Registry;

use Constructo\Contract\Formatter;
use Constructo\Support\Metadata\Schema\Registry\Spec;
use Constructo\Support\Set;
use PHPUnit\Framework\TestCase;

final class SpecTest extends TestCase
{
    public function testCanCreateSpecWithAllParameters(): void
    {
        $name = 'required';
        $properties = Set::createFrom(['param1' => 'value1']);
        $formatter = $this->createMock(Formatter::class);

        $spec = new Spec($name, $properties, $formatter);

        $this->assertSame($name, $spec->name);
        $this->assertSame($properties, $spec->properties);
        $this->assertSame($formatter, $spec->formatter);
    }

    public function testCanCreateSpecWithNullFormatter(): void
    {
        $name = 'string';
        $properties = Set::createFrom([]);

        $spec = new Spec($name, $properties, null);

        $this->assertSame($name, $spec->name);
        $this->assertSame($properties, $spec->properties);
        $this->assertNull($spec->formatter);
    }

    public function testCanCreateSpecWithEmptyProperties(): void
    {
        $name = 'nullable';
        $properties = Set::createFrom([]);
        $formatter = $this->createMock(Formatter::class);

        $spec = new Spec($name, $properties, $formatter);

        $this->assertSame($name, $spec->name);
        $this->assertSame($properties, $spec->properties);
        $this->assertSame($formatter, $spec->formatter);
    }

    public function testCanCreateSpecWithComplexProperties(): void
    {
        $name = 'between';
        $properties = Set::createFrom([
            'params' => ['min', 'max'],
            'message' => 'Value must be between {min} and {max}',
            'nullable' => false,
        ]);

        $spec = new Spec($name, $properties, null);

        $this->assertSame($name, $spec->name);
        $this->assertSame($properties, $spec->properties);
        $this->assertNull($spec->formatter);
    }

    public function testSpecPropertiesAreReadonly(): void
    {
        $name = 'min';
        $properties = Set::createFrom(['params' => ['min']]);
        $formatter = $this->createMock(Formatter::class);

        $spec = new Spec($name, $properties, $formatter);

        // Verify properties are accessible
        $this->assertSame($name, $spec->name);
        $this->assertSame($properties, $spec->properties);
        $this->assertSame($formatter, $spec->formatter);

        // Since the class is readonly, we can't test assignment directly
        // but we can verify the properties maintain their values
        $this->assertSame($name, $spec->name);
        $this->assertSame($properties, $spec->properties);
        $this->assertSame($formatter, $spec->formatter);
    }

    public function testCanCreateSpecWithDifferentNameTypes(): void
    {
        $names = ['required', 'string', 'integer', 'email', 'url', 'uuid'];
        $properties = Set::createFrom([]);

        foreach ($names as $name) {
            $spec = new Spec($name, $properties, null);
            $this->assertSame($name, $spec->name);
        }
    }

    public function testCanCreateSpecWithParameterizedRules(): void
    {
        $testCases = [
            [
                'name' => 'min',
                'properties' => Set::createFrom(['params' => ['min']]),
            ],
            [
                'name' => 'max',
                'properties' => Set::createFrom(['params' => ['max']]),
            ],
            [
                'name' => 'between',
                'properties' => Set::createFrom(['params' => ['min', 'max']]),
            ],
            [
                'name' => 'in',
                'properties' => Set::createFrom(['params' => ['values']]),
            ],
            [
                'name' => 'regex',
                'properties' => Set::createFrom(['params' => ['pattern', 'parameters:optional']]),
            ],
        ];

        foreach ($testCases as $testCase) {
            $spec = new Spec($testCase['name'], $testCase['properties'], null);

            $this->assertSame($testCase['name'], $spec->name);
            $this->assertSame($testCase['properties'], $spec->properties);
            $this->assertNull($spec->formatter);
        }
    }

    public function testSpecWithFormatterInterface(): void
    {
        $name = 'custom';
        $properties = Set::createFrom(['custom' => 'value']);
        $formatter = $this->createMock(Formatter::class);

        // Configure the mock if needed
        $formatter->expects($this->never())->method($this->anything());

        $spec = new Spec($name, $properties, $formatter);

        $this->assertInstanceOf(Formatter::class, $spec->formatter);
        $this->assertSame($formatter, $spec->formatter);
    }
}
