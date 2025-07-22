<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Serialize;

use DateTime;
use Faker\Factory;
use Constructo\Core\Serialize\Builder;
use Constructo\Exception\Adapter\NotResolved;
use Constructo\Exception\AdapterException;
use Constructo\Support\Reflective\Notation;
use Constructo\Support\Set;
use Constructo\Test\Stub\Deep;
use Constructo\Test\Stub\EntityStub;
use Constructo\Test\Stub\Formatter\ArrayFormatter;
use Constructo\Test\Stub\NoConstructor;
use Constructo\Test\Stub\Type\Intersected;
use Constructo\Test\Stub\Type\SingleBacked;
use Constructo\Test\Stub\Variety;
use PHPUnit\Framework\TestCase;
use stdClass;

use function Constructo\Json\encode;

final class BuilderTest extends TestCase
{
    public function testMapWithValidValues(): void
    {
        $entityClass = EntityStub::class;
        $values = [
            'id' => 1,
            'price' => 19.99,
            'name' => 'Test',
            'is_active' => true,
            'tags' => encode(['tag1', 'tag2']),
            'more' => new NoConstructor(),
            'enum' => 'one',
        ];

        $mapper = new Builder(formatters: [
            'array' => new ArrayFormatter(),
        ]);
        $instance = $mapper->build($entityClass, Set::createFrom($values));

        $this->assertInstanceOf($entityClass, $instance);
        $this->assertSame(1, $instance->id);
        $this->assertSame(19.99, $instance->price);
        $this->assertSame('Test', $instance->name);
        $this->assertTrue($instance->isActive);
        $this->assertSame(['tag1', 'tag2'], $instance->tags);
        $this->assertNull($instance->createdAt);
        $this->assertEquals(SingleBacked::ONE, $instance->enum);
    }

    public function testMapWithMissingOptionalValue(): void
    {
        $values = [
            'id' => 1,
            'price' => 19.99,
            'name' => 'Test',
            'is_active' => true,
            'more' => new NoConstructor(),
            'created_at' => '1981-08-13T00:00:00+00:00',
            'enum' => SingleBacked::ONE,
        ];

        $mapper = new Builder();
        $instance = $mapper->build(EntityStub::class, Set::createFrom($values));

        $this->assertInstanceOf(EntityStub::class, $instance);
        $this->assertSame(1, $instance->id);
        $this->assertSame(19.99, $instance->price);
        $this->assertSame('Test', $instance->name);
        $this->assertTrue($instance->isActive);
        $this->assertSame([], $instance->tags);
        $this->assertInstanceOf(DateTime::class, $instance->createdAt);
    }

    public function testMapWithErrors(): void
    {
        $entityClass = EntityStub::class;
        $values = [
            'id' => 'invalid',
            'name' => 'Test',
            'is_active' => true,
            'tags' => ['tag1', 'tag2'],
            'more' => new DateTime(),
            'no' => 'invalid',
            'enum' => false,
        ];

        try {
            $mapper = new Builder();
            $mapper->build($entityClass, Set::createFrom($values));
        } catch (AdapterException $exception) {
            $errors = $exception->getUnresolved();
            $this->assertContainsOnlyInstancesOf(NotResolved::class, $errors);
            $messages = [
                "The value for 'id' must be of type 'int' and 'string' was given.",
                "The value for 'price' is required and was not given.",
                "The value for 'more' must be of type 'Constructo\\Test\\Stub\\NoConstructor' and 'DateTime' was given.",
                "The value for 'no' must be of type 'Constructo\\Test\\Stub\\NoParameters' and 'string' was given.",
                "The value for 'enum' must be of type 'Constructo\\Test\\Stub\\Type\\SingleBacked' and 'bool' was given.",
            ];
            foreach ($messages as $message) {
                if ($this->hasErrorMessage($errors, $message)) {
                    continue;
                }
                $this->fail(sprintf('Error message "%s" not found', $message));
            }
        }
    }

    public function testMapWithNoConstructor(): void
    {
        $values = [];

        $mapper = new Builder();
        $instance = $mapper->build(NoConstructor::class, Set::createFrom($values));

        $this->assertInstanceOf(NoConstructor::class, $instance);
    }

    public function testMapWithReflectionError(): void
    {
        $this->expectException(AdapterException::class);
        $this->expectExceptionMessage('Class "NonExistentClass" does not exist');

        $values = [
            'id' => 1,
            'price' => 19.99,
            'name' => 'Test',
            'is_active' => true,
            'more' => new NoConstructor(),
        ];

        $mapper = new Builder();
        $mapper->build('NonExistentClass', Set::createFrom($values));
    }

    public function testMapWithReflectionInvalidArgsError(): void
    {
        $this->expectException(AdapterException::class);

        $values = [];

        $mapper = new Builder();
        $mapper->build(EntityStub::class, Set::createFrom($values));
    }

    public function testEdgeTypeCases(): void
    {
        $values = [
            'union' => 1,
            'intersection' => new Intersected(),
            'nested' => [
                'id' => 1,
                'price' => 19.99,
                'name' => 'Test',
                'is_active' => true,
                'more' => new NoConstructor(),
                'tags' => ['tag1', 'tag2'],
            ],
            'whatever' => new stdClass(),
        ];

        $mapper = new Builder();
        $instance = $mapper->build(Variety::class, Set::createFrom($values));

        $this->assertInstanceOf(Variety::class, $instance);
        $this->assertSame(1, $instance->union);
        $this->assertInstanceOf(Intersected::class, $instance->intersection);
        $this->assertInstanceOf(EntityStub::class, $instance->nested);
        $this->assertInstanceOf(stdClass::class, $instance->getWhatever());
    }

    public function testEdgeTypeCasesFailure(): void
    {
        $values = [];

        $builder = new Builder(Notation::NONE);
        $this->expectException(AdapterException::class);
        $this->expectExceptionMessage(
            'Adapter failed with 3 error(s). The errors are: '
            . '"The value for \'union\' is required and was not given.", '
            . '"The value for \'intersection\' is required and was not given.", '
            . '"The value for \'nested\' is required and was not given."'
        );
        $builder->build(Variety::class, Set::createFrom($values));
    }

    public function testEdgeTypeCasesFailureNested(): void
    {
        $values = [
            'nested' => [
                'id' => '1',
                'price' => 19.99,
                'name' => 'Test',
                'is_active' => 1,
            ],
        ];

        $builder = new Builder();
        $this->expectException(AdapterException::class);
        $this->expectExceptionMessage(
            'Adapter failed with 5 error(s). The errors are: '
            . '"The value for \'union\' is required and was not given.", '
            . '"The value for \'intersection\' is required and was not given.", '
            . '"The value for \'nested.id\' must be of type \'int\' and \'string\' was given.", '
            . '"The value for \'nested.isActive\' must be of type \'bool\' and \'int\' was given.", '
            . '"The value for \'nested.more\' is required and was not given."'
        );

        $builder->build(Variety::class, Set::createFrom($values));
    }

    public function testShouldBuildDeepDown(): void
    {
        $generator = Factory::create();
        $values = [
            'what' => $generator->word(),
            'deep_down' => [
                'deep_deep_down' => [
                    'stub' => [
                        'id' => $generator->numberBetween(1, 100),
                        'price' => $generator->randomFloat(),
                        'name' => $generator->name(),
                        'is_active' => $generator->boolean(),
                    ],
                    'builtin' => [
                        $generator->word(),
                        $generator->numberBetween(1, 100),
                        $generator->randomFloat(),
                        $generator->boolean(),
                        $generator->words(),
                    ],
                ],
                'builtin' => [
                    $generator->word(),
                    $generator->numberBetween(1, 100),
                    $generator->word(),
                    $generator->boolean(),
                    $generator->words(),
                    null,
                ],
            ],
        ];

        $this->expectException(AdapterException::class);
        $this->expectExceptionMessage(
            'Adapter failed with 3 error(s). The errors are: '
            . '"The value for \'deepDown.deepDeepDown.stub.more\' is required and was not given.", '
            . '"The value for \'deepDown.deepDeepDown.empty\' is required and was not given.", '
            . '"The value for \'deepDown.builtin.float\' must be of type \'float\' and \'string\' was given."'
        );
        $builder = new Builder();
        $builder->build(Deep::class, Set::createFrom($values));
    }

    private function hasErrorMessage(array $errors, string $message): bool
    {
        foreach ($errors as $error) {
            if ($error->message === $message) {
                return true;
            }
        }
        return false;
    }
}
