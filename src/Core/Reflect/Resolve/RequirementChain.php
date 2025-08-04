<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Resolve;

use Constructo\Core\Reflect\Chain;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Registry\Specs;
use ReflectionParameter;

class RequirementChain extends Chain
{
    public function __construct(
        private readonly ?Field $parent = null,
        ?Specs $specs = null,
    ) {
        parent::__construct();
    }

    public function resolve(ReflectionParameter $parameter, Field $field, array $path): void
    {
        $this->applyConditionalRule($parameter, $field);
        $this->applyRequirementRule($parameter, $field);
        $this->applyNullabilityRule($parameter, $field);

        parent::resolve($parameter, $field, $path);
    }

    private function applyNullabilityRule(ReflectionParameter $parameter, Field $field): void
    {
        if ($parameter->allowsNull()) {
            $field->nullable();
        }
    }

    private function applyConditionalRule(ReflectionParameter $parameter, Field $field): void
    {
        if ($this->parent?->hasRule('sometimes')) {
            $field->sometimes();
            return;
        }

        if ($parameter->isOptional() && $parameter->isDefaultValueAvailable()) {
            $field->sometimes();
        }
    }

    private function applyRequirementRule(ReflectionParameter $parameter, Field $field): void
    {
        if ($this->parent?->hasRule('sometimes')) {
            return;
        }

        if ($this->isStrictlyRequired($parameter)) {
            $field->required();
            return;
        }

        if ($this->shouldBePresent($parameter)) {
            $field->present();
            return;
        }

        if ($this->shouldBeFilled($parameter)) {
            $field->filled();
        }

        if ($this->shouldUseSometimesRequired($parameter)) {
            $field->required();
        }
    }

    private function isStrictlyRequired(ReflectionParameter $parameter): bool
    {
        return ! $parameter->isOptional() &&
            ! $parameter->isDefaultValueAvailable() &&
            ! $parameter->allowsNull();
    }

    private function shouldBePresent(ReflectionParameter $parameter): bool
    {
        return ! $parameter->isOptional() &&
            ! $parameter->isDefaultValueAvailable() &&
            $parameter->allowsNull();
    }

    private function shouldBeFilled(ReflectionParameter $parameter): bool
    {
        return $parameter->isOptional() &&
            ! $parameter->isDefaultValueAvailable() &&
            ! $parameter->allowsNull();
    }

    private function shouldUseSometimesRequired(ReflectionParameter $parameter): bool
    {
        return $parameter->isOptional() &&
            $parameter->isDefaultValueAvailable() &&
            ! $parameter->allowsNull();
    }
}
