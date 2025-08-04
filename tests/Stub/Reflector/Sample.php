<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Reflector;

use Constructo\Test\Stub\Domain\Entity\Game;
use Constructo\Test\Stub\Type\TestIntEnum;
use Constructo\Test\Stub\Type\TestStringEnum;

class Sample
{
    public readonly string $name;

    public function __construct(
        public readonly string $requiredField,
        public readonly ?string $requiredNullableField,
        public readonly TestStringEnum $requiredEnumField,
        public readonly ?TestStringEnum $requiredNullableEnumField,
        string $processedField,
        public readonly string $defaultStringField = 'default_value',
        public readonly ?string $defaultNullField = null,
        public readonly ?array $optionalArrayField = null,
        public readonly ?Game $optionalObjectField = null,
        public readonly TestIntEnum $defaultEnumField = TestIntEnum::ONE,
        ?string $processedNullableField = null,
    ) {
        $this->processedField = strtoupper($processedField);
        $this->processedNullableField = $processedNullableField
            ?
            strtolower($processedNullableField)
            :
            null;
    }

    public readonly string $processedField;
    public readonly ?string $processedNullableField;
}
