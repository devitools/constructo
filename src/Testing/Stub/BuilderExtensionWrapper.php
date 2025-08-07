<?php

declare(strict_types=1);

namespace Constructo\Testing\Stub;

use Constructo\Core\Serialize\Builder;
use Constructo\Testing\BuilderExtension;
use Constructo\Testing\MakeExtension;

/**
 * @internal
 */
class BuilderExtensionWrapper
{
    use BuilderExtension;
    use MakeExtension;

    public function getBuilder(): Builder
    {
        return $this->builder();
    }
}
