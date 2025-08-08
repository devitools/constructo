<?php

namespace Examples\Reflector;

require_once __DIR__ . '/../../vendor/autoload.php';

use Constructo\Factory\ReflectorFactory;
use DateTime;

use function array_export;

// Defina sua entidade informando os valores das propriedades no construtor
readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
        public DateTime $birthDate,
        public bool $isActive = true,
        public array $tags = [],
    ) {
    }
}

// Crie o reflector e obtenha o schema da entidade
$schema = ReflectorFactory::createFrom()->make()->reflect(User::class);

echo "# Regras de validação \n";
echo array_export($schema->rules(), 1);
echo "\n";
