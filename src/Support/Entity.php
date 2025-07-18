<?php

declare(strict_types=1);

namespace Morph\Support;

use JsonSerializable;
use Morph\Contract\Exportable;

class Entity implements Exportable, JsonSerializable
{
    public function export(): object
    {
        return (object) get_object_vars($this);
    }

    public function jsonSerialize(): object
    {
        return $this->export();
    }
}
