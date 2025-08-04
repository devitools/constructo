<?php

declare(strict_types=1);

namespace Constructo\Support\Metadata\Schema\Field;

use Constructo\Support\Metadata\Schema\Registry\Spec;
use InvalidArgumentException;

use function array_filter;
use function Constructo\Cast\stringify;
use function count;
use function str_contains;

class Rules
{
    private array $rules = [];

    public function all(): array
    {
        return array_map(fn (Rule $rule): string => stringify($rule), array_values($this->rules));
    }

    public function register(Spec $spec, array $arguments): void
    {
        $this->validate($spec, $arguments);
        $rule = new Rule($spec, $arguments);
        $this->rules[$rule->key] = $rule;
    }

    public function has(string $rule): bool
    {
        return isset($this->rules[$rule]);
    }

    private function validate(Spec $spec, array $arguments): void
    {
        $params = $spec->properties->get('params');
        if (! is_array($params)) {
            return;
        }
        $required = array_filter($params, fn (mixed $item) => ! str_contains(stringify($item), ':optional'));
        if (count($required) === 0 || count($arguments) >= count($required)) {
            return;
        }
        $expected = count($params);
        $names = implode(', ', array_map(fn (mixed $item) => stringify($item), $required));
        $given = count($arguments);
        $message = sprintf(
            'Spec rule %s expects %d (%s) parameters, %d given.',
            $spec->name,
            $expected,
            $names,
            $given
        );
        throw new InvalidArgumentException($message);
    }
}
