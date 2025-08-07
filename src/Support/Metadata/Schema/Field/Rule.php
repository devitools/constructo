<?php

declare(strict_types=1);

namespace Constructo\Support\Metadata\Schema\Field;

use Constructo\Support\Metadata\Schema\Registry\Spec;
use JsonSerializable;
use Stringable;

use function Constructo\Cast\arrayify;
use function Constructo\Cast\stringify;
use function is_string;
use function sprintf;

readonly class Rule implements Stringable, JsonSerializable
{
    public string $key;

    public function __construct(
        public Spec $spec,
        public array $arguments = [],
    ) {
        $kind = $spec->properties->get('kind');
        if (! is_string($kind)) {
            $kind = null;
        }
        $this->key = $kind ?? $spec->name;
    }

    public function __toString(): string
    {
        $arguments = $this->arguments;
        if ($this->spec->formatter) {
            $arguments = arrayify($this->spec->formatter->format($this->arguments));
        }
        if (empty($arguments)) {
            return $this->spec->name;
        }
        $arguments = array_map(fn (mixed $item): string => $this->enforce($item), $arguments);
        $definition = implode(',', $arguments);
        return sprintf('%s:%s', $this->spec->name, $definition);
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    private function enforce(mixed $item): string
    {
        return match (true) {
            is_string($item) => $item,
            is_iterable($item) => implode(
                ',',
                array_map(
                    fn ($element): string => $this->enforce($element),
                    is_array($item)
                        ? $item
                        : iterator_to_array($item, false)
                )
            ),
            default => stringify($item),
        };
    }
}
