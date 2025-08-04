<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Metadata\Schema\Registry;

use Constructo\Core\Metadata\Schema\Registry\Spec;
use Constructo\Core\Metadata\Schema\Registry\Specs;
use Constructo\Support\Set;
use Constructo\Test\Stub\Formatter\ArrayFormatter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class SpecsTest extends TestCase
{
    public function testCanCreateEmptySpecs(): void
    {
        $specs = new Specs();

        $this->assertFalse($specs->has('nonexistent'));
        $this->assertNull($specs->get('nonexistent'));
    }

    public function testCanRegisterAndRetrieveSpec(): void
    {
        $specs = new Specs();
        $data = ['param1' => 'value1'];

        $specs->register('required', $data);

        $this->assertTrue($specs->has('required'));
        $spec = $specs->get('required');
        $this->assertInstanceOf(Spec::class, $spec);
        $this->assertSame('required', $spec->name);
        $this->assertInstanceOf(Set::class, $spec->properties);
        $this->assertSame('value1', $spec->properties->get('param1'));
        $this->assertNull($spec->formatter);
    }

    public function testCanRegisterSpecWithFormatter(): void
    {
        $specs = new Specs();
        $data = ['formatter' => ArrayFormatter::class];

        $specs->register('formatted', $data);

        $this->assertTrue($specs->has('formatted'));
        $spec = $specs->get('formatted');
        $this->assertInstanceOf(Spec::class, $spec);
        $this->assertSame('formatted', $spec->name);
        $this->assertInstanceOf(ArrayFormatter::class, $spec->formatter);
    }

    public function testRegisterConvertsNameToSnakeCase(): void
    {
        $specs = new Specs();

        $specs->register('CamelCase', []);
        $specs->register('kebab-case', []);
        $specs->register('PascalCase', []);

        // Both original and snake_case versions should work because has() converts to snake_case
        $this->assertTrue($specs->has('camel_case'));
        $this->assertTrue($specs->has('kebab_case'));
        $this->assertTrue($specs->has('pascal_case'));
        $this->assertTrue($specs->has('CamelCase'));
        $this->assertTrue($specs->has('kebab-case'));
        $this->assertTrue($specs->has('PascalCase'));
    }

    public function testGetConvertsNameToSnakeCase(): void
    {
        $specs = new Specs();
        $specs->register('test_spec', []);

        $this->assertInstanceOf(Spec::class, $specs->get('testSpec'));
        $this->assertInstanceOf(Spec::class, $specs->get('test-spec'));
        $this->assertInstanceOf(Spec::class, $specs->get('TestSpec'));
        $this->assertInstanceOf(Spec::class, $specs->get('test_spec'));
    }

    public function testHasConvertsNameToSnakeCase(): void
    {
        $specs = new Specs();
        $specs->register('test_spec', []);

        $this->assertTrue($specs->has('testSpec'));
        $this->assertTrue($specs->has('test-spec'));
        $this->assertTrue($specs->has('TestSpec'));
        $this->assertTrue($specs->has('test_spec'));
    }

    public function testGetReturnsNullForNonExistentSpec(): void
    {
        $specs = new Specs();

        $this->assertNull($specs->get('nonexistent'));
    }

    public function testRegisterOverwritesExistingSpec(): void
    {
        $specs = new Specs();

        $specs->register('test', ['param1' => 'value1']);
        $specs->register('test', ['param2' => 'value2']);

        $spec = $specs->get('test');
        $this->assertSame('value2', $spec->properties->get('param2'));
        $this->assertNull($spec->properties->get('param1'));
    }

    public function testRegisterWithComplexData(): void
    {
        $specs = new Specs();
        $data = [
            'params' => ['min', 'max'],
            'required' => true,
            'nullable' => false,
            'default' => 'test',
        ];

        $specs->register('complex', $data);

        $spec = $specs->get('complex');
        $this->assertSame(['min', 'max'], $spec->properties->get('params'));
        $this->assertTrue($spec->properties->get('required'));
        $this->assertFalse($spec->properties->get('nullable'));
        $this->assertSame('test', $spec->properties->get('default'));
    }

    public function testDefineFormatterWithNullFormatter(): void
    {
        $specs = new Specs();

        $specs->register('no_formatter', []);

        $spec = $specs->get('no_formatter');
        $this->assertNull($spec->formatter);
    }

    public function testDefineFormatterThrowsExceptionForNonStringFormatter(): void
    {
        $specs = new Specs();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Formatter must be a valid class-string, integer given.');

        $specs->register('invalid', ['formatter' => 123]);
    }

    public function testDefineFormatterThrowsExceptionForNonExistentClass(): void
    {
        $specs = new Specs();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Formatter must be a valid class-string, string given.');

        $specs->register('invalid', ['formatter' => 'NonExistentClass']);
    }

    public function testDefineFormatterThrowsExceptionForNonFormatterClass(): void
    {
        $specs = new Specs();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Formatter must implement Constructo\Contract\Formatter, object given.');

        $specs->register('invalid', ['formatter' => stdClass::class]);
    }

    public function testDefineFormatterThrowsExceptionForArrayFormatter(): void
    {
        $specs = new Specs();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Formatter must be a valid class-string, array given.');

        $specs->register('invalid', ['formatter' => []]);
    }

    public function testDefineFormatterThrowsExceptionForBooleanFormatter(): void
    {
        $specs = new Specs();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Formatter must be a valid class-string, boolean given.');

        $specs->register('invalid', ['formatter' => true]);
    }

    public function testDefineFormatterWithNullFormatterValue(): void
    {
        $specs = new Specs();

        // Null formatter should not throw exception, it should be treated as no formatter
        $specs->register('null_formatter', ['formatter' => null]);

        $spec = $specs->get('null_formatter');
        $this->assertNull($spec->formatter);
    }

    public function testMultipleSpecsRegistration(): void
    {
        $specs = new Specs();

        $specs->register('required', []);
        $specs->register('string', ['type' => 'string']);
        $specs->register('min', ['params' => ['min']]);
        $specs->register('max', ['params' => ['max']]);

        $this->assertTrue($specs->has('required'));
        $this->assertTrue($specs->has('string'));
        $this->assertTrue($specs->has('min'));
        $this->assertTrue($specs->has('max'));

        $stringSpec = $specs->get('string');
        $this->assertSame('string', $stringSpec->properties->get('type'));

        $minSpec = $specs->get('min');
        $this->assertSame(['min'], $minSpec->properties->get('params'));
    }
}
