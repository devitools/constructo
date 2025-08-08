<?php

namespace Examples\Builder;

require_once __DIR__ . '/../../vendor/autoload.php';

use Constructo\Core\Serialize\Builder;
use Constructo\Support\Set;
use DateTime;

// Defina sua entidade informando os valores das propriedades no construtor
readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
        public DateTime $birthDate,
        public bool $isActive = true,
        public array $tags = [],
    ) {}
}

// Monte um set com os dados (de JSON, banco de dados, etc.)
$set = Set::createFrom([
    'id' => 1,
    'name' => 'João Silva',
    'birth_date' => '1981-08-13',
    'is_active' => true,
    'tags' => ['nice', 'welcome'],
]);

// Crie um novo builder e use-o para construir o objeto
$user = (new Builder())->build(User::class, $set);

echo "# Usuário: \n";
echo sprintf("#   ID: %s\n", $user->id);
echo sprintf("#   Nome: %s\n", $user->name);
echo sprintf("#   Ativo: %s\n", $user->isActive ? 'Sim' : 'Não');
echo sprintf("#   Tags: %s\n", implode(', ', $user->tags));
echo sprintf("#   Data de Nascimento: %s\n", $user->birthDate->format('Y-m-d'));
