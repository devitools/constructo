<?php

declare(strict_types=1);

namespace Constructo\Test\_;

use InvalidArgumentException;
use RuntimeException;
use PHPUnit\Framework\TestCase;
use Constructo\Support\Set;

use function Constructo\Crypt\decrypt;
use function Constructo\Crypt\encrypt;
use function Constructo\Crypt\group;
use function Constructo\Crypt\ungroup;

final class CryptFunctionsTest extends TestCase
{
    public function testShouldEncrypt(): void
    {
        $encrypted = encrypt('test');
        $this->assertIsString($encrypted);
        $this->assertNotEquals('test', $encrypted);
        $this->assertJson(base64_decode($encrypted));
        $this->assertEquals('test', decrypt($encrypted));
    }

    public function testEncryptWithCustomKey(): void
    {
        $customKey = base64_encode('my-custom-key-32-bytes-long!!');
        $plaintext = 'secret message';

        $encrypted = encrypt($plaintext, $customKey);
        $decrypted = decrypt($encrypted, $customKey);

        $this->assertEquals($plaintext, $decrypted);
    }

    public function testDecryptWithWrongKeyThrowsException(): void
    {
        $key1 = base64_encode('key1-32-bytes-long-for-testing!');
        $key2 = base64_encode('key2-32-bytes-long-for-testing!');

        $encrypted = encrypt('test', $key1);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Decryption failed');
        decrypt($encrypted, $key2);
    }

    public function testGroupCreatesValidJsonStructure(): void
    {
        $algo = 'aes-256-cbc';
        $salt = base64_encode('test-salt-16-byte');
        $ciphertext = base64_encode('test-ciphertext');

        $result = group($algo, $salt, $ciphertext);
        $decoded = json_decode($result, true);

        $this->assertIsArray($decoded);
        $this->assertEquals($algo, $decoded['algo']);
        $this->assertEquals($salt, $decoded['salt']);
        $this->assertEquals($ciphertext, $decoded['data']);
    }

    public function testUngroupReturnsSetWithCorrectData(): void
    {
        $algo = 'aes-256-cbc';
        $salt = base64_encode('test-salt-16-byte');
        $ciphertext = base64_encode('test-ciphertext');

        $grouped = group($algo, $salt, $ciphertext);
        $encrypted = base64_encode($grouped);

        $result = ungroup($encrypted);

        $this->assertInstanceOf(Set::class, $result);
        $this->assertEquals($algo, $result->get('algo'));
        $this->assertEquals($salt, $result->get('salt'));
        $this->assertEquals($ciphertext, $result->get('data'));
    }

    public function testUngroupWithInvalidFormatThrowsException(): void
    {
        $invalidEncrypted = base64_encode('invalid json');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid encrypted format');
        ungroup($invalidEncrypted);
    }

    public function testUngroupWithMissingFieldsThrowsException(): void
    {
        $incompleteData = json_encode(['algo' => 'aes-256-cbc']); // missing salt and data
        $encrypted = base64_encode($incompleteData);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid encrypted format');
        ungroup($encrypted);
    }

    public function testDecryptWithUnknownAlgorithmThrowsException(): void
    {
        $data = json_encode([
            'algo' => 'unknown-algorithm',
            'salt' => base64_encode('test-salt'),
            'data' => base64_encode('test-data')
        ]);
        $encrypted = base64_encode($data);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown encryption algorithm');
        decrypt($encrypted);
    }

    public function testEncryptDecryptWithEmptyString(): void
    {
        $encrypted = encrypt('');
        $decrypted = decrypt($encrypted);

        $this->assertEquals('', $decrypted);
    }

    public function testEncryptDecryptWithSpecialCharacters(): void
    {
        $plaintext = 'Special chars: áéíóú ñ ç 中文 🚀';
        $encrypted = encrypt($plaintext);
        $decrypted = decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }
}
