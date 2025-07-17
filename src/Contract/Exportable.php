<?php

declare(strict_types=1);

namespace Morph\Contract;

interface Exportable
{
    public function export(): mixed;
}
