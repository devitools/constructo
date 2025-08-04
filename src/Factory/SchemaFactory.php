<?php

declare(strict_types=1);

namespace Constructo\Factory;

use Constructo\Contract\Schema\SpecsFactory;
use Constructo\Core\Metadata\Schema;
use Constructo\Core\Metadata\Schema\Field\Fieldset;

class SchemaFactory
{
    public function __construct(private readonly SpecsFactory $specsFactory)
    {
    }

    public function make(): Schema
    {
        $specs = $this->specsFactory->make();
        return new Schema($specs, new Fieldset());
    }
}
