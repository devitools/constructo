<?php

declare(strict_types=1);

namespace Constructo\Test\Support;

use Constructo\Exception\SchemaException;
use Constructo\Support\Payload;
use PHPUnit\Framework\TestCase;

final class PayloadTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $payload = Payload::createFrom(['key' => 'value']);
        $this->assertEquals('value', $payload->get('key'));
    }

    public function testMagicGetExistingKey(): void
    {
        $payload = new Payload(['key' => 'value']);
        $this->assertEquals('value', $payload->key);
    }

    public function testMagicGetNonExistingKey(): void
    {
        $payload = new Payload(['key' => 'value']);
        $this->assertNull($payload->non_existing_key);
    }

    public function testMagicIssetExistingKey(): void
    {
        $payload = new Payload(['key' => 'value']);
        $this->assertTrue(isset($payload->key));
    }

    public function testMagicIssetNonExistingKey(): void
    {
        $payload = new Payload(['key' => 'value']);
        $this->assertFalse(isset($payload->non_existing_key));
    }

    public function testMagicSetThrowsException(): void
    {
        $payload = new Payload(['key' => 'value']);
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('Cannot modify payload properties');
        $payload->__set('key', 'new_value');
    }

    public function testResolveWithScalarValue(): void
    {
        $payload = new Payload(['key' => 'value']);
        $this->assertEquals('value', $payload->key);
    }

    public function testResolveWithNestedArrayCreatesPayload(): void
    {
        $payload = new Payload(['nested' => ['inner_key' => 'inner_value']]);
        $nested = $payload->nested;
        $this->assertInstanceOf(Payload::class, $nested);
        $this->assertEquals('inner_value', $nested->inner_key);
    }

    public function testResolveWithArrayWithNonStringKeysReturnsArray(): void
    {
        $payload = new Payload(['mixed' => ['string_key' => 'value', 0 => 'indexed']]);
        $mixed = $payload->mixed;
        $this->assertIsArray($mixed);
        $this->assertEquals('value', $mixed['string_key']);
        $this->assertEquals('indexed', $mixed[0]);
    }

    public function testNestedPayloadAccess(): void
    {
        $payload = new Payload([
            'level1' => [
                'level2' => [
                    'level3' => 'deep_value'
                ]
            ]
        ]);
        $this->assertEquals('deep_value', $payload->level1?->level2?->level3);
    }

    public function testInheritedGetMethod(): void
    {
        $payload = new Payload(['key' => 'value']);
        $this->assertEquals('value', $payload->get('key'));
        $this->assertNull($payload->get('non_existing_key'));
    }

    public function testInheritedAtMethod(): void
    {
        $payload = new Payload(['key' => 'value']);
        $this->assertEquals('value', $payload->at('key'));
    }

    public function testInheritedAtMethodThrowsException(): void
    {
        $payload = new Payload(['key' => 'value']);
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage("Field 'non_existing_key' not found.");
        $payload->at('non_existing_key');
    }

    public function testInheritedWithMethod(): void
    {
        $payload = new Payload(['key' => 'value']);
        $newPayload = $payload->with('new_key', 'new_value');
        $this->assertEquals('new_value', $newPayload->get('new_key'));
        $this->assertEquals('value', $newPayload->get('key'));
    }

    public function testInheritedAlongMethod(): void
    {
        $payload = new Payload(['key' => 'value']);
        $newPayload = $payload->along(['new_key' => 'new_value']);
        $this->assertEquals('new_value', $newPayload->get('new_key'));
        $this->assertEquals('value', $newPayload->get('key'));
    }

    public function testInheritedHasMethod(): void
    {
        $payload = new Payload(['key' => 'value']);
        $this->assertTrue($payload->has('key'));
        $this->assertFalse($payload->has('non_existing_key'));
    }

    public function testInheritedHasNotMethod(): void
    {
        $payload = new Payload(['key' => 'value']);
        $this->assertFalse($payload->hasNot('key'));
        $this->assertTrue($payload->hasNot('non_existing_key'));
    }

    public function testInheritedToArrayMethod(): void
    {
        $payload = new Payload(['key' => 'value', 'nested' => ['inner' => 'inner_value']]);
        $array = $payload->toArray();
        $this->assertIsArray($array);
        $this->assertEquals('value', $array['key']);
        $this->assertIsArray($array['nested']);
        $this->assertEquals('inner_value', $array['nested']['inner']);
    }

    public function testInvalidValuesArray(): void
    {
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('Values must be an array.');
        new Payload('invalid');
    }

    public function testInvalidKeysInArray(): void
    {
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('All keys must be strings.');
        new Payload(['value', 5 => 'foo', 'key' => 'value']);
    }

    public function testResolveWithNullValue(): void
    {
        $payload = new Payload(['null_key' => null]);
        $this->assertNull($payload->null_key);
    }

    public function testResolveWithEmptyArray(): void
    {
        $payload = new Payload(['empty_array' => []]);
        $resolved = $payload->empty_array;
        $this->assertInstanceOf(Payload::class, $resolved);
    }

    public function testMagicMethodsWithComplexData(): void
    {
        $data = [
            'user' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'profile' => [
                    'age' => 30,
                    'settings' => [
                        'theme' => 'dark',
                        'notifications' => true
                    ]
                ]
            ]
        ];

        $payload = new Payload($data);

        $this->assertTrue(isset($payload->user));
        $this->assertEquals('John Doe', $payload->user->name);
        $this->assertEquals('john@example.com', $payload->user->email);
        $this->assertEquals(30, $payload->user->profile->age);
        $this->assertEquals('dark', $payload->user->profile->settings->theme);
        $this->assertTrue($payload->user->profile->settings->notifications);
    }
}
