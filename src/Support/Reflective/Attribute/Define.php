<?php

declare(strict_types=1);

namespace Morph\Support\Reflective\Attribute;

use Attribute;
use Morph\Support\Reflective\Definition\Type;
use Morph\Support\Reflective\Definition\TypeExtended;

#[Attribute]
readonly class Define
{
    /**
     * @var array<Type|TypeExtended>
     */
    public array $types;

    public function __construct(Type|TypeExtended ...$type)
    {
        $this->types = $type;
    }
}
