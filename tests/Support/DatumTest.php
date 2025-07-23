<?php

declare(strict_types=1);

namespace Constructo\Test\Support;

use Constructo\Support\Datum;
use Error;
use Exception;
use PHPUnit\Framework\TestCase;

final class DatumTest extends TestCase
{
    public function testExportIncludesErrorData(): void
    {
        $throwable = new Exception('Test error', 123);
        $data = ['foo' => 'bar', 'baz' => 42];
        $datum = new Datum($throwable, $data);

        $exported = $datum->export();

        $this->assertEquals('bar', $exported->foo);
        $this->assertEquals(42, $exported->baz);
        $this->assertTrue(property_exists($exported, '@error'));
        $this->assertEquals('Test error', $exported->{'@error'}['message']);
        $this->assertEquals(123, $exported->{'@error'}['code']);
        $this->assertEquals(__FILE__, $exported->{'@error'}['file']);
        $this->assertIsInt($exported->{'@error'}['line']);
    }

    public function testJsonSerializeReturnsExport(): void
    {
        $throwable = new Exception('Json error');
        $data = ['a' => 1];
        $datum = new Datum($throwable, $data);
        $json = json_encode($datum);
        $decoded = json_decode($json);
        $this->assertEquals(1, $decoded->a);
        $this->assertEquals('Json error', $decoded->{'@error'}->message);
    }

    public function testGetReturnsSetValue(): void
    {
        $throwable = new Exception('Get error');
        $data = ['x' => 'y'];
        $datum = new Datum($throwable, $data);
        $this->assertEquals('y', $datum->x);
    }

    public function testIssetReturnsTrueForExistingKey(): void
    {
        $throwable = new Exception('Isset error');
        $data = ['key' => 'value'];
        $datum = new Datum($throwable, $data);
        $this->assertTrue(isset($datum->key));
        $this->assertFalse(isset($datum->not_exists));
    }

    public function testSetThrowsError(): void
    {
        $this->expectException(Error::class);
        $throwable = new Exception('Set error');
        $data = ['foo' => 'bar'];
        $datum = new Datum($throwable, $data);
        $datum->foo = 'baz';
    }
}
