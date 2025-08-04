<?php

declare(strict_types=1);

namespace Constructo\Contract\Reflect;

use Constructo\Support\Metadata\Schema\Registry\Types;

interface TypesFactory
{
    public function make(): Types;
}
