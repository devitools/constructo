<?php

declare(strict_types=1);

namespace Constructo\Support;

use JsonSerializable;
use Constructo\Contract\Exportable;

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
