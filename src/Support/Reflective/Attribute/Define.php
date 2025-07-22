<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Attribute;

use Attribute;
use Constructo\Support\Reflective\Definition\Type;
use Constructo\Support\Reflective\Definition\TypeExtended;

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
