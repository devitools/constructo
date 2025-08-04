<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Reflector;

use Constructo\Test\Stub\Domain\Entity\Game;
use Constructo\Test\Stub\Type\TestIntEnum;
use Constructo\Test\Stub\Type\TestStringEnum;

readonly class Sample
{
    public string $name;

    public function __construct(
        public string $requiredField,
        public ?string $requiredNullableField,
        public TestStringEnum $requiredEnumField,
        public ?TestStringEnum $requiredNullableEnumField,
        string $processedField,
        public string $defaultStringField = 'default_value',
        public ?string $defaultNullField = null,
        public ?array $optionalArrayField = null,
        public ?Game $optionalObjectField = null,
        public TestIntEnum $defaultEnumField = TestIntEnum::ONE,
        ?string $processedNullableField = null,
    ) {
        $this->processedField = strtoupper($processedField);
        $this->processedNullableField = $processedNullableField
            ?
            strtolower($processedNullableField)
            :
            null;
    }

    public string $processedField;
    public ?string $processedNullableField;
}
