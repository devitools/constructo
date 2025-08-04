<?php

declare(strict_types=1);

namespace Constructo\Contract\Schema;

use Constructo\Core\Metadata\Schema\Registry\Specs;

interface SpecsFactory
{
    public function make(): Specs;
}
