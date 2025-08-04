<?php

declare(strict_types=1);

namespace Constructo\Factory;

use Constructo\Contract\Reflect\TypesFactory;
use Constructo\Support\Metadata\Schema\Registry\Types;

readonly class DefaultTypesFactory implements TypesFactory
{
    public function __construct(private array $types)
    {
    }

    public function make(): Types
    {
        return new Types($this->types);
    }
}
