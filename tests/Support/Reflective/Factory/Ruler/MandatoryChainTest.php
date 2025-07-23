<?php

declare(strict_types=1);

namespace Constructo\Test\Support\Reflective\Factory\Ruler;

use Constructo\Support\Reflective\Factory\Ruler\MandatoryChain;
use Constructo\Support\Reflective\Ruleset;
use Constructo\Test\Stub\Domain\Entity\Command\GameCommand;
use Constructo\Test\Stub\EntityStub;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MandatoryChainTest extends TestCase
{
    public function testRequiredParameterResolution(): void
    {
        $chain = new MandatoryChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(GameCommand::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $chain->resolve($parameters[0], $ruleset);

        $this->assertEquals(['required'], $ruleset->get('name'));
    }

    public function testOptionalParameterResolution(): void
    {
        $chain = new MandatoryChain();
        $ruleset = new Ruleset();

        $reflection = new ReflectionClass(EntityStub::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $chain->resolve($parameters[7], $ruleset);

        $this->assertEquals(['sometimes'], $ruleset->get('tags'));
    }
}
