<?php

declare(strict_types=1);

namespace Morph\Json;

use JsonException;

use function Morph\Cast\arrayify;
use function Morph\Cast\stringify;

if (! function_exists(__NAMESPACE__ . '\decode')) {
    function decode(string $json): ?array
    {
        try {
            return arrayify(json_decode($json, true, 512, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
            return null;
        }
    }
}

if (! function_exists(__NAMESPACE__ . '\encode')) {
    function encode(array $data): ?string
    {
        try {
            return stringify(json_encode($data, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
            return null;
        }
    }
}
