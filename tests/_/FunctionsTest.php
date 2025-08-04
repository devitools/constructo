<?php

declare(strict_types=1);

namespace Constructo\Test\_;

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function testShouldRequireFunctions(): void
    {
        $files = [
            'src/_/cast.php',
            'src/_/crypt.php',
            'src/_/json.php',
            'src/_/notation.php',
            'src/_/util.php',
        ];
        foreach ($files as $file) {
            $filename = __DIR__ . '/../../' . $file;
            $this->assertFileExists($filename, sprintf("File '%s' does not exist", $file));
            require $filename;
        }
    }
}
