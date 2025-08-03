<?php

declare(strict_types=1);

namespace Constructo\Core\Metadata\Schema\Element;

use JsonSerializable;
use Stringable;

use function Constructo\Cast\arrayify;
use function Constructo\Cast\stringify;
use function sprintf;

class Rule implements Stringable, JsonSerializable
{
    public readonly string $key;

    public function __construct(
        public readonly Spec $spec,
        public readonly array $arguments = [],
    ) {
        $this->key = $spec->properties->get('kind') ?? $spec->name;
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
        $arguments = array_map(fn (mixed $item): string => stringify($item), $arguments);
        $definition = implode(',', $arguments);
        return sprintf('%s:%s', $this->spec->name, $definition);
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
