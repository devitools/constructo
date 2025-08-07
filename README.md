# Constructo

**O serializador e deserializador definitivo para PHP**

[![Versão PHP](https://img.shields.io/badge/php-%5E8.3-blue.svg)](https://php.net/)
[![Licença](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Composer](https://img.shields.io/badge/composer-devitools%2Fconstructo-orange.svg)](https://packagist.org/packages/devitools/constructo)

Constructo é uma poderosa biblioteca PHP que fornece capacidades avançadas de serialização e deserialização para objetos PHP. Ela permite conversão perfeita entre objetos e arrays/JSON, com suporte para estruturas aninhadas complexas, conversão de tipos, validação e formatação personalizada.

## 🚀 Funcionalidades

- **Conversão Bidirecional**: Serialize objetos para arrays/JSON e deserialize de volta para objetos tipados
- **Segurança de Tipos**: Suporte completo ao sistema de tipos do PHP 8.3+ incluindo union types, backed enums e propriedades readonly
- **Mapeamento Inteligente**: Mapeamento automático de propriedades com conversão de snake_case para camelCase
- **Formatadores Personalizados**: Sistema de formatação extensível para transformações de dados customizadas
- **Objetos Aninhados**: Manipule hierarquias de objetos complexas e coleções perfeitamente
- **Tratamento de Erros**: Relatório de erros abrangente com contexto detalhado
- **Validação**: Validação integrada com suporte a atributos personalizados
- **Manipulação de Data/Hora**: Análise e formatação inteligente de DateTime
- **Coleções**: Suporte de primeira classe para coleções tipadas
- **Injeção de Dependência**: Resolução automática de dependência para construção de objetos

## 📦 Instalação

Instale o Constructo via Composer:

```bash
composer require devitools/constructo
```

### Requisitos

- PHP 8.3 ou superior
- ext-json

## 🔧 Início Rápido

### Serialização simples e rápida

```php
<?php
# ...
use Constructo\Core\Serialize\Builder;
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

echo "Usuário: \n";
echo sprintf("  ID: %s\n", $user->id);
echo sprintf("  Nome: %s\n", $user->name);
echo sprintf("  Ativo: %s\n", $user->isActive);
echo sprintf("  Tags: %s\n", implode(', ', $user->tags));
echo sprintf("  Data de Nascimento: %s\n", $user->birthDate->format('Y-m-d'));
```

### Desserialização prática e direta

```php
<?php

use Constructo\Core\Deserialize\Demolisher;

// Crie uma instância
$user = new User(1, 'João Silva', 'joao@exemplo.com', true, ['admin', 'usuario']);

// Serialize para objeto/array
$demolisher = new Demolisher();
$data = $demolisher->demolish($user);

echo json_encode($data);
// Saída: {"id":1,"name":"João Silva","email":"joao@exemplo.com","isActive":true,"tags":["admin","usuario"]}
```

## 📋 Uso Avançado do Builder

### Formatadores Personalizados no Builder

Constructo suporta formatadores personalizados para transformação de dados durante a deserialização:

```php
<?php

use Constructo\Core\Serialize\Builder;

// Formatador personalizado para arrays
class ArrayFormatter
{
    public function __invoke($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }
}

// Use com Builder
$builder = new Builder(formatters: [
    'array' => new ArrayFormatter(),
]);

$data = [
    'id' => 1,
    'name' => 'Maria Santos',
    'tags' => '["desenvolvedor", "php"]' // String JSON será convertida para array
];

$user = $builder->build(User::class, Set::createFrom($data));
echo implode(', ', $user->tags); // "desenvolvedor, php"
```

### Objetos Aninhados Complexos com Builder

```php
<?php

class Address extends Entity
{
    public function __construct(
        public readonly string $street,
        public readonly string $city,
        public readonly string $country
    ) {}
}

class User extends Entity
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly Address $address,
        public readonly ?DateTime $createdAt = null
    ) {}
}

// Dados aninhados
$data = [
    'id' => 1,
    'name' => 'João Silva',
    'address' => [
        'street' => 'Rua Principal, 123',
        'city' => 'São Paulo',
        'country' => 'Brasil'
    ],
    'created_at' => '2023-01-15T10:30:00+00:00'
];

$builder = new Builder();
$user = $builder->build(User::class, Set::createFrom($data));

echo $user->address->city; // "São Paulo"
echo $user->createdAt->format('d/m/Y'); // "15/01/2023"
```

### Suporte a Backed Enums com Builder

```php
<?php

enum Status: string
{
    case ACTIVE = 'ativo';
    case INACTIVE = 'inativo';
    case PENDING = 'pendente';
}

class Order extends Entity
{
    public function __construct(
        public readonly int $id,
        public readonly Status $status,
        public readonly float $amount
    ) {}
}

$data = [
    'id' => 1,
    'status' => 'ativo',  // String será convertida para enum
    'amount' => 99.99
];

$builder = new Builder();
$order = $builder->build(Order::class, Set::createFrom($data));

echo $order->status->value; // "ativo"
```

### Tratamento de Erros com Datum

Quando a deserialização falha, o Constructo fornece informações detalhadas de erro:

```php
<?php

use Constructo\Support\Datum;
use Constructo\Exception\AdapterException;

try {
    $result = $builder->build(User::class, Set::createFrom($invalidData));
} catch (AdapterException $e) {
    // Crie um objeto Datum com detalhes do erro
    $datum = new Datum($e, $invalidData);
    
    $errorData = $datum->export();
    // Contém dados originais mais '@error' com detalhes da exceção
}
```

## 📋 Uso Avançado do Demolisher

### Formatadores Personalizados no Demolisher

```php
<?php

use Constructo\Core\Deserialize\Demolisher;

// Formatador de string personalizado
$stringFormatter = fn($value) => sprintf('[%s]', $value);

// Use com Demolisher
$demolisher = new Demolisher(formatters: [
    'string' => $stringFormatter,
]);

$user = new User(1, 'Ana Costa', 'ana@exemplo.com');
$data = $demolisher->demolish($user);

echo $data->name; // "[Ana Costa]"
```

### Trabalhando com Coleções

```php
<?php

use Constructo\Contract\Collectable;
use Constructo\Type\Collection;

class UserCollection extends Collection implements Collectable
{
    protected function getItemClass(): string
    {
        return User::class;
    }
}

// Serialize coleção
$collection = new UserCollection();
$collection->push($user1);
$collection->push($user2);

$demolisher = new Demolisher();
$arrayData = $demolisher->demolishCollection($collection);
```

## 🛠️ Funções Utilitárias

Constructo inclui várias funções utilitárias para operações comuns:

### Helpers JSON

```php
<?php

use function Constructo\Json\decode;
use function Constructo\Json\encode;

$array = decode('{"name":"João","age":30}');
$json = encode(['name' => 'João', 'age' => 30]);
```

### Conversão de Tipos

```php
<?php

use function Constructo\Cast\arrayify;
use function Constructo\Cast\stringify;

$array = arrayify($data);  // Converte para array com segurança
$string = stringify($value);  // Converte para string com segurança
```

### Extração de Dados

```php
<?php

use function Constructo\Util\extractString;
use function Constructo\Util\extractInt;
use function Constructo\Util\extractBool;
use function Constructo\Util\extractArray;

$name = extractString($data, 'name', 'padrão');
$age = extractInt($data, 'age', 0);
$active = extractBool($data, 'is_active', false);
$tags = extractArray($data, 'tags', []);
```

## 🧪 Extensões de Teste

Constructo fornece utilitários de teste para facilitar os testes:

```php
<?php

use Constructo\Testing\BuilderExtension;
use Constructo\Testing\MakeExtension;
use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    use BuilderExtension, MakeExtension;
    
    public function testSerialization(): void
    {
        $user = $this->builder()->build(User::class, Set::createFrom($data));
        $this->assertInstanceOf(User::class, $user);
    }
}
```

## 📚 Conceitos Centrais

### Classe Base Entity

Estenda a classe `Entity` para obter suporte automático de serialização:

```php
<?php

use Constructo\Support\Entity;

class MyEntity extends Entity
{
    // Automaticamente implementa Exportable e JsonSerializable
    // Fornece método export() que retorna objeto com todas as propriedades públicas
}
```

### Classe Set

A classe `Set` é usada para gerenciar coleções de dados com segurança de tipos:

```php
<?php

use Constructo\Support\Set;

$set = Set::createFrom(['key' => 'valor']);
$set = new Set(['key' => 'valor']);
$value = $set->get('key', 'padrão');
$array = $set->toArray();
```

### Classe Value

Para manipular valores individuais com validação e transformação:

```php
<?php

use Constructo\Support\Value;

$value = new Value('alguns dados');
// Fornece vários métodos para manipulação e validação de valores
```

## 🔍 Funcionalidades Avançadas

### Geração de Schema

Constructo pode gerar schemas para seus objetos:

```php
<?php

use Constructo\Factory\SchemaFactory;
use Constructo\Factory\DefaultSpecsFactory;

$schemaFactory = new SchemaFactory(new DefaultSpecsFactory());
$schema = $schemaFactory->make();
```

### Reflexão e Metadados

Capacidades avançadas de reflexão para introspecção de objetos:

```php
<?php

use Constructo\Support\Reflective\Engine;
use Constructo\Factory\ReflectorFactory;

$reflectorFactory = new ReflectorFactory();
$reflector = $reflectorFactory->make();
```

### Cache

Suporte integrado de cache para melhor performance:

```php
<?php

use Constructo\Support\Cache;

$cache = new Cache();
// Fornece mecanismos de cache para dados de reflexão e schemas
```

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para enviar um Pull Request. Para mudanças importantes, abra primeiro uma issue para discutir o que você gostaria de alterar.

### Configuração de Desenvolvimento

1. Clone o repositório
2. Instale as dependências: `composer install`
3. Execute os testes: `composer test`
4. Execute o linting: `composer lint:phpcs`
5. Execute análise estática: `composer lint:phpstan`

### Ferramentas de Qualidade de Código

O projeto usa várias ferramentas de qualidade de código:

- **PHPUnit** para testes
- **PHPStan** para análise estática
- **PHP_CodeSniffer** para estilo de código
- **PHPMD** para detecção de bagunça
- **Psalm** para análise estática adicional
- **Rector** para modernização de código

## 📄 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 🏢 Sobre a Devitools

Constructo é desenvolvido e mantido pela [Devitools](https://devi.tools/). Nós nos especializamos em criar ferramentas de desenvolvimento poderosas e bibliotecas para aplicações web modernas.

---

Para mais informações e exemplos de uso avançado, visite nossa documentação em [devi.tools/constructo](https://devi.tools/constructo).
