<?php

declare(strict_types=1);

namespace Constructo\Factory;

use Constructo\Contract\Reflect\SpecsFactory;
use Constructo\Support\Metadata\Schema;
use Constructo\Support\Metadata\Schema\Field\Fieldset;

readonly class SchemaFactory
{
    public function __construct(private SpecsFactory $specsFactory)
    {
    }

    public function make(): Schema
    {
        $specs = $this->specsFactory->make();
        return new Schema($specs, new Fieldset());
    }
}
