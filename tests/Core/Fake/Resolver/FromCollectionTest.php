<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver\FromCollection;
use Constructo\Support\Set;
use Constructo\Test\Stub\Domain\Collection\GameCollection;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionParameter;

final class FromCollectionTest extends TestCase
{
    public function testShouldResolveCollectionParameter(): void
    {
        $resolver = new FromCollection();
        $method = new ReflectionMethod($this, 'methodWithCollection');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNotNull($result);
        $this->assertIsArray($result->get());
        $this->assertNotEmpty($result->get());
    }

    public function testShouldReturnNullForNonCollectionParameter(): void
    {
        $resolver = new FromCollection();
        $method = new ReflectionMethod($this, 'methodWithString');
        $parameter = $method->getParameters()[0];
        $presets = Set::createFrom([]);

        $result = $resolver->resolve($parameter, $presets);

        $this->assertNull($result);
    }

    private function methodWithCollection(GameCollection $collection): void
    {
    }

    private function methodWithString(string $value): void
    {
    }
}
