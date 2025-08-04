<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Reflective\Engine;

use Constructo\Support\Reflective\Engine;
use ReflectionParameter;

class TestableEngine extends Engine
{
    public function __construct(array $formatters = [])
    {
        parent::__construct(formatters: $formatters);
    }

    public function testSelectFormatter(string $candidate): ?callable
    {
        return $this->selectFormatter($candidate);
    }

    public function testDetectCollectionName(ReflectionParameter $parameter): ?string
    {
        return $this->detectCollectionName($parameter);
    }

    public function testFormatTypeName(?\ReflectionType $type): ?string
    {
        return $this->formatTypeName($type);
    }
}
