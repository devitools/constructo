<?php

/** @noinspection TypoSafeNamingInspection */

declare(strict_types=1);

namespace Constructo\Support\Metadata\Schema;

use BadMethodCallException;
use Closure;
use Constructo\Support\Metadata\Schema\Field\Rules;
use Constructo\Support\Metadata\Schema\Registry\Spec;
use Constructo\Support\Metadata\Schema\Registry\Specs;

/**
 * # Global setup
 * @method self bail()
 * @method self sometimes()
 *
 * # Requirements
 * @method self required()
 * @method self requiredIf(string $field, mixed $value)
 * @method self requiredUnless(string $field, mixed $value)
 * @method self requiredWith(string ...$fields)
 * @method self requiredWithAll(string ...$fields)
 * @method self requiredWithout(string ...$fields)
 * @method self requiredWithoutAll(string ...$fields)
 * @method self filled()
 * @method self present()
 * @method self nullable()
 *
 * # Types
 * @method self string()
 * @method self integer()
 * @method self numeric()
 * @method self boolean()
 * @method self array()
 * @method self date()
 * @method self json()
 * @method self file()
 * @method self image()
 * @method self email()
 * @method self url()
 * @method self activeUrl()
 * @method self uuid()
 * @method self ip()
 * @method self ipv4()
 * @method self ipv6()
 * @method self timezone()
 *
 * # Constraints
 * ## Numbers constraints
 * @method self between(int $min, int $max)
 * @method self digits(int $digits)
 * @method self digitsBetween(int $min, int $max)
 * ## Among fields constraints
 * @method self gt(string $field)
 * @method self gte(string $field)
 * @method self lt(string $field)
 * @method self lte(string $field)
 * @method self same(string $field)
 * @method self different(string $field)
 * @method self confirmed()
 * @method self distinct()
 * @method self inArray(string $field)
 * ## String constraints
 * @method self accepted()
 * @method self alpha()
 * @method self alphaDash()
 * @method self alphaNum()
 * @method self startsWith(string ...$values)
 * ## Date constraints
 * @method self after(string $date)
 * @method self afterOrEqual(string $date)
 * @method self before(string $date)
 * @method self beforeOrEqual(string $date)
 * @method self dateEquals(string $date)
 * @method self dateFormat(string $format)
 * ## Multiple types constraints
 * @method self size(int $size)
 * @method self min(int $min)
 * @method self max(int $max)
 * ## File constraints
 * @method self mimes(string ...$mimes)
 * @method self mimetypes(string ...$mimetypes)
 * @method self dimensions(array $constraints)
 *
 * # Database
 * @method self unique(string $table, string $column = null, mixed $except = null, string $idColumn = null)
 * @method self exists(string $table, string $column = null)
 *
 * # Behaviors
 * @method self in(array|string $items)
 * @method self notIn(array $items)
 * @method self regex(string $pattern, Closure|array $parameters = null)
 * @method self notRegex(string $pattern)
 *
 * @method self map(Closure|string $mapping)
 * @method self unavailable()
 * @method self available()
 */
final class Field
{
    public const array MAPPING = ['map'];

    public const array VISIBILITY = [
        'unavailable',
        'available',
    ];


    private bool $available = true;

    private Closure|string|null $map = null;

    /**
     * @var class-string<object>|null
     */
    private ?string $source = null;

    public function __construct(
        public readonly Specs $specs,
        private readonly Rules $rules,
        public readonly string $name,
    ) {
    }

    /**
     * @return array<string>
     */
    public function rules(): array
    {
        return $this->rules->all();
    }

    public function mapping(): Closure|string|null
    {
        return $this->map;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    /**
     * @param class-string<object> $source
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     * @return class-string<object>|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    public function hasRule(string $rule): bool
    {
        return $this->rules->has($rule);
    }

    public function __call(string $name, array $arguments): self
    {
        if ($this->is(self::MAPPING, $name)) {
            $this->handleMapping(...$arguments);
            return $this;
        }
        if ($this->is(self::VISIBILITY, $name)) {
            $this->handleVisibility($name);
            return $this;
        }
        $spec = $this->specs->get($name);
        if ($spec instanceof Spec) {
            $this->handleSpec($spec, $arguments);
            return $this;
        }
        throw new BadMethodCallException(sprintf("Entry '%s' is not supported.", $name));
    }

    private function is(array $haystack, string $needle): bool
    {
        return in_array($needle, $haystack, true);
    }

    private function handleMapping(mixed $mapping): void
    {
        if ($mapping instanceof Closure || is_string($mapping)) {
            $this->map = $mapping;
        }
    }

    private function handleVisibility(string $name): void
    {
        $this->available = $name === 'available';
    }

    private function handleSpec(Spec $spec, array $arguments): void
    {
        $this->rules->register($spec, $arguments);
    }
}
