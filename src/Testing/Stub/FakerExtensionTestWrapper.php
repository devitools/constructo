<?php

declare(strict_types=1);

namespace Constructo\Testing\Stub;

use Constructo\Core\Fake\Faker;
use Constructo\Testing\FakerExtension;
use Constructo\Testing\MakeExtension;
use Faker\Generator;

/**
 * @internal
 */
class FakerExtensionTestWrapper
{
    use FakerExtension;
    use MakeExtension;

    public function getFaker(): Faker
    {
        return $this->faker();
    }

    public function getGenerator(): Generator
    {
        return $this->generator();
    }
}
