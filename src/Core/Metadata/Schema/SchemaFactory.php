<?php

declare(strict_types=1);

namespace Constructo\Core\Metadata\Schema;

use Constructo\Core\Metadata\Schema;
use Constructo\Core\Metadata\Schema\Field\Fieldset;
use Constructo\Core\Metadata\Schema\Registry\SpecsFactory;

class SchemaFactory
{
    public function __construct(private readonly SpecsFactory $specsFactory)
    {
    }

    public function make(): Schema
    {
        $schemaRegistry = $this->specsFactory->make();
        return new Schema($schemaRegistry, new Fieldset());
    }
}
