<?php

declare(strict_types=1);

namespace Morph\Support\Reflective;

use Stringable;

use function Serendipity\Type\Cast\stringify;

final class Ruleset
{
    /**
     * @var array<string, array<string>>
     */
    public const array RULES = [
        'accepted' => [],
        'active_url' => [],
        'after' => ['date'],
        'after_or_equal' => ['date'],
        'alpha' => [],
        'alpha_dash' => [],
        'alpha_num' => [],
        'array' => [],
        'bail' => [],
        'before' => ['date'],
        'before_or_equal' => ['date'],
        'between' => ['min', 'max'],
        'boolean' => [],
        'confirmed' => [],
        'date' => [],
        'date_equals' => ['date'],
        'date_format' => ['format'],
        'different' => ['field'],
        'digits' => ['value'],
        'digits_between' => ['min', 'max'],
        'dimensions' => [],
        'distinct' => [],
        'email' => [],
        'exists' => ['table', 'column'],
        'file' => [],
        'filled' => [],
        'gt' => ['field'],
        'gte' => ['field'],
        'image' => [],
        'in' => ['...'],
        'in_array' => ['another_field'],
        'integer' => [],
        'ip' => [],
        'ipv4' => [],
        'ipv6' => [],
        'json' => [],
        'lt' => ['field'],
        'lte' => ['field'],
        'max' => ['value'],
        'mimetypes' => ['...'],
        'mimes' => ['...'],
        'min' => ['value'],
        'not_in' => ['...'],
        'not_regex' => ['pattern'],
        'nullable' => [],
        'numeric' => [],
        'present' => [],
        'regex' => ['pattern'],
        'required' => [],
        'required_if' => ['another_field', 'value'],
        'required_unless' => ['another_field', 'value'],
        'required_with' => ['...'],
        'required_with_all' => ['...'],
        'required_without' => ['...'],
        'required_without_all' => ['...'],
        'same' => ['field'],
        'size' => ['value'],
        'starts_with' => ['...'],
        'string' => [],
        'timezone' => [],
        'unique' => ['table', 'column', 'except', 'column_id'],
        'url' => [],
        'uuid' => [],
        'sometimes' => [],
    ];

    /**
     * @var array<string, array<string>>
     */
    private array $rules = [];

    public function add(string $field, string|Stringable $rule, float|int|string|Stringable ...$parameters): bool
    {
        $rule = stringify($rule);
        if (! isset(self::RULES[$rule])) {
            return false;
        }

        if (! isset($this->rules[$field])) {
            $this->rules[$field] = [];
        }
        $this->rules[$field][] = empty($parameters) ? $rule : sprintf('%s:%s', $rule, implode(',', $parameters));
        return true;
    }

    /**
     * @return array<string>
     */
    public function get(string $field): array
    {
        return $this->rules[$field] ?? [];
    }

    /**
     * @return array<string, array<string>>
     */
    public function all(): array
    {
        return $this->rules;
    }
}
