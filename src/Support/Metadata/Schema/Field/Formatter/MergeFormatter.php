<?php

declare(strict_types=1);

namespace Constructo\Support\Metadata\Schema\Field\Formatter;

use BackedEnum;
use BadMethodCallException;
use Constructo\Contract\Formatter;

use function array_map;
use function class_exists;
use function Constructo\Cast\arrayify;
use function Constructo\Cast\stringify;
use function count;
use function is_string;
use function is_subclass_of;

class MergeFormatter implements Formatter
{
    public function format(mixed $value, mixed $option = null): array
    {
        if (! is_array($value) || count($value) < 1) {
            throw new BadMethodCallException('MergeFormatter requires an array with at least one element.');
        }
        [$items] = $value;
        if (is_string($items) && class_exists($items) && is_subclass_of($items, BackedEnum::class)) {
            $items = $items::cases();
        }
        $callback = fn (mixed $item) => match (true) {
            $item instanceof BackedEnum => $item->value,
            default => stringify($item),
        };
        return array_map($callback, arrayify($items));
    }
}
