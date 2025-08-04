<?php

declare(strict_types=1);

namespace Constructo\Contract\Reflect;

use Constructo\Support\Metadata\Schema\Registry\Specs;

interface SpecsFactory
{
    public function make(): Specs;
}
