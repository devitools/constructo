<?php

declare(strict_types=1);

namespace Constructo\Support;

use Constructo\Contract\Exportable;
use JsonSerializable;

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
