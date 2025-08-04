<?php

declare(strict_types=1);

namespace Constructo\Support\Metadata\Schema\Registry;

use Constructo\Type\Timestamp;

use function Constructo\Cast\stringify;

readonly class Types
{
    private array $types;

    public function __construct(array $types = [])
    {
        $this->types = array_merge($this->defaults(), $types);
    }

    public function has(string $source): bool
    {
        return isset($this->types[$source]);
    }

    public function get(string $source): ?string
    {
        $type = $this->types[$source] ?? null;
        return $type
            ? stringify($type)
            : null;
    }

    protected function defaults(): array
    {
        return [
            'DateTime' => 'date',
            'DateTimeImmutable' => 'date',
            'DateTimeInterface' => 'date',
            Timestamp::class => 'date',
        ];
    }
}
