<?php

namespace Examples\Demolisher;

require_once __DIR__ . '/../../vendor/autoload.php';

use Constructo\Core\Deserialize\Demolisher;
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

// Instancie o objeto que deseja destruir
$user = new User(
    id: 1,
    name: 'João Silva',
    birthDate: new DateTime('1981-08-13'),
    isActive: true,
    tags: ['nice', 'welcome'],
);

// Crie um novo demolisher e use-o para destruir o objeto
$object = (new Demolisher())->demolish($user);

echo "# Usuário: \n";
echo sprintf("#   ID: %s\n", $object->id);
echo sprintf("#   Nome: %s\n", $object->name);
echo sprintf("#   Ativo: %s\n", $object->is_active ? 'Sim' : 'Não');
echo sprintf("#   Tags: %s\n", implode(', ', $object->tags));
echo sprintf("#   Data de Nascimento: %s\n", $object->birth_date);
