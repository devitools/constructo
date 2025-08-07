<?php

namespace Examples\Demolisher;

require_once __DIR__ . '/../../vendor/autoload.php';

use Constructo\Core\Deserialize\Demolisher;
use Constructo\Support\Set;
use Constructo\Type\Timestamp;

// Defina sua entidade informando os valores das propriedades no construtor
readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
        public Timestamp $birthDate,
        public bool $isActive = true,
        public array $tags = [],
    ) {}
}

// Instancie o objeto que deseja destruir
$user = new User(
    id: 1,
    name: 'João Silva',
    birthDate: new Timestamp('1981-08-13'),
    isActive: true,
    tags: ['nice', 'welcome'],
);

// Crie um novo demolisher e use-o para destruir o objeto
$object = (new Demolisher())->demolish($user);

$set = Set::createFrom((array) $object);
echo "# Usuário: \n";
echo sprintf("#   ID: %s\n", $set->get('id'));
echo sprintf("#   Nome: %s\n", $set->get('name'));
echo sprintf("#   Ativo: %s\n", $set->get('is_active') ? 'Sim' : 'Não');
echo sprintf("#   Tags: %s\n", implode(', ', $set->get('tags')));
echo sprintf("#   Data de Nascimento: %s\n", $set->get('birth_date'));
