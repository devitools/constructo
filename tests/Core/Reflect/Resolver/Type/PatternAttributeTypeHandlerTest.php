<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Reflect\Resolver\Type;

use Constructo\Core\Reflect\Resolver\Type\PatternAttributeTypeHandler;
use Constructo\Core\Serialize\Builder;
use Constructo\Factory\DefaultSpecsFactory;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use Constructo\Support\Reflective\Attribute\Pattern;
use Constructo\Testing\MakeExtension;
use PHPUnit\Framework\TestCase;
use ReflectionAttribute;
use ReflectionParameter;

class PatternAttributeTypeHandlerTest extends TestCase
{
    use MakeExtension;

    private PatternAttributeTypeHandler $handler;
    private Specs $specs;

    protected function setUp(): void
    {
        $this->handler = new PatternAttributeTypeHandler();
        $builder = $this->make(Builder::class);
        $specsData = [
            'regex' => [
                'params' => [
                    'pattern',
                    'parameters:optional',
                ],
            ],
        ];

        $specsFactory = new DefaultSpecsFactory($builder, $specsData);
        $this->specs = $specsFactory->make();
    }

    public function testResolvePatternAttributeWithEmailRegex(): void
    {
        $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
        $patternAttribute = new Pattern($emailPattern);
        $parameter = $this->createParameterWithPatternAttribute($patternAttribute);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('regex'));
    }

    public function testResolvePatternAttributeWithPhoneRegex(): void
    {
        $phonePattern = '/^\+?[1-9]\d{1,14}$/';
        $patternAttribute = new Pattern($phonePattern);
        $parameter = $this->createParameterWithPatternAttribute($patternAttribute);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('regex'));
    }

    public function testResolvePatternAttributeWithSimplePattern(): void
    {
        $simplePattern = '/^[A-Z]{3}$/';
        $patternAttribute = new Pattern($simplePattern);
        $parameter = $this->createParameterWithPatternAttribute($patternAttribute);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('regex'));
    }

    public function testDoesNotResolveWhenNoPatternAttribute(): void
    {
        $parameter = $this->createParameterWithoutPatternAttribute();
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertFalse($field->hasRule('regex'));
    }

    public function testResolvePatternAttributeWithComplexPattern(): void
    {
        $complexPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/';
        $patternAttribute = new Pattern($complexPattern);
        $parameter = $this->createParameterWithPatternAttribute($patternAttribute);
        $field = new Field($this->specs, new Rules(), 'test');

        $this->handler->resolve($parameter, $field);

        $this->assertTrue($field->hasRule('regex'));
    }

    private function createParameterWithPatternAttribute(Pattern $patternAttribute): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);
        $reflectionAttribute = $this->createMock(ReflectionAttribute::class);

        $reflectionAttribute->method('newInstance')->willReturn($patternAttribute);

        $parameter->method('getAttributes')
            ->with(Pattern::class)
            ->willReturn([$reflectionAttribute]);

        return $parameter;
    }

    private function createParameterWithoutPatternAttribute(): ReflectionParameter
    {
        $parameter = $this->createMock(ReflectionParameter::class);

        $parameter->method('getAttributes')
            ->with(Pattern::class)
            ->willReturn([]);

        return $parameter;
    }
}
