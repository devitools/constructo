# AGENTS.md

This file provides guidance to AI coding agents when working with code in this repository.

## Project Overview

Constructo is a PHP library for powerful object serialization and deserialization. It converts between PHP objects and arrays/JSON with full type safety, supporting complex nested structures, backed enums, readonly properties, and custom formatters.

**Key Concepts:**
- **Builder**: Serializes data (arrays/JSON) into typed PHP objects
- **Demolisher**: Deserializes PHP objects back into arrays/JSON
- **Set**: Type-safe container for managing key-value data
- **Entity**: Base class providing automatic export() and JSON serialization
- **Faker**: Test data generation utility using FakerPHP

## Development Commands

**All commands use Docker via Makefile.** The project uses `compose.yml` with a `constructo-app` container.

### Initial Setup
```bash
# Setup project (prune, install dependencies, start containers)
make setup

# Start containers
make up

# Stop containers
make down

# Prune containers and volumes
make prune

# Enter container bash
make bash
```

### Testing
```bash
# Run all tests (generates HTML coverage in tests/.phpunit/html)
docker-compose exec constructo vendor/bin/phpunit

# Run specific test file
docker-compose exec constructo vendor/bin/phpunit tests/Core/Serialize/BuilderTest.php

# Run specific test method
docker-compose exec constructo vendor/bin/phpunit --filter testMethodName

# Run tests with specific options
docker-compose exec constructo vendor/bin/phpunit --testdox --colors=always
```

### Code Quality
```bash
# Run all linters
make lint

# Individual linters
make lint-phpcs      # Code style (PSR-12)
make lint-phpstan    # Static analysis (level 10)
make lint-phpmd      # Mess detection
make lint-rector     # Modernization check (dry-run)
make lint-psalm      # Additional static analysis

# Auto-fix code issues
make fix             # Runs rector and php-cs-fixer
```

### CI Pipeline
```bash
# Full CI check (lint + test)
make ci
```

### Composer
```bash
# Install dependencies
make install

# Dump autoload
make dump
```

## Architecture

### Core Components

**Serialization (Data → Objects):**
- `Core/Serialize/Builder`: Main entry point for building objects from data
- `Core/Serialize/Resolver/*`: Chain of responsibility pattern for resolving parameter values
  - `ValidateValue`: Validates input data
  - `DependencyValue`: Resolves constructor dependencies
  - `TypeMatched`: Matches types for nested objects
  - `BackedEnumValue`: Converts strings to backed enums
  - `AttributeValue`: Applies custom attributes
  - `CollectionValue`: Handles collections/arrays
  - `FormatValue`: Applies custom formatters
  - `NoValue`: Falls back to default values

**Deserialization (Objects → Data):**
- `Core/Deserialize/Demolisher`: Converts objects to arrays/JSON
- `Core/Deserialize/Resolve/*`: Chain for processing object properties
  - `DoNothingChain`: Passes through simple values
  - `DependencyChain`: Handles nested dependencies
  - `AttributeChain`: Processes custom attributes
  - `CollectionChain`: Demolishes collections
  - `DateChain`: Formats DateTime objects
  - `FormatterChain`: Applies custom formatters

**Reflection System:**
- `Support/Reflective/Engine`: Base class with reflection utilities and formatter selection
- `Core/Reflect/Reflector`: Introspects classes for metadata
- `Support/Reflective/Factory/Target`: Wraps ReflectionClass with parameter access

**Testing Utilities:**
- `Core/Fake/Faker`: Generates fake data for testing (extends FakerPHP)
- `Testing/BuilderExtension`: PHPUnit trait providing builder() helper
- `Testing/MakeExtension`: PHPUnit trait providing make() helper for test instances
- `Testing/FakerExtension`: PHPUnit trait providing faker() helper

**Helper Functions:**
Located in `src/_/` directory:
- `cast.php`: Type conversion (arrayify, stringify, mapify, etc.)
- `json.php`: JSON encoding/decoding
- `util.php`: Data extraction (extractString, extractInt, extractBool, extractArray)
- `notation.php`: Snake_case ↔ camelCase conversion
- `crypt.php`: Encryption utilities

### Design Patterns

**Chain of Responsibility:**
Both Builder and Demolisher use resolver chains. Each resolver attempts to handle the parameter, or passes it to the next in the chain via `then()`.

**Factory Pattern:**
- `Factory/ReflectorFactory`: Creates Reflector instances
- `Factory/SchemaFactory`: Generates schemas from class definitions
- `Support/Reflective/Factory/Target`: Creates reflection targets

**Template Method:**
`Engine` abstract class defines the common reflection/formatting logic, while `Builder` and `Demolisher` implement specific serialization flows.

### Key Types

- **Set**: Immutable key-value container with validation (all keys must be strings)
- **Value**: Wrapper for individual values with validation/transformation
- **Datum**: Error handling wrapper that combines exceptions with original data
- **Entity**: Base class for domain objects needing serialization
- **Collection**: Base for typed collections implementing `Collectable`
- **Timestamp**: Custom DateTime wrapper for timestamp handling

## Code Conventions

### Type Safety
- Use PHP 8.3+ features: readonly properties, union types, backed enums
- Always declare strict types: `declare(strict_types=1);`
- PHPStan level 10 must pass (strictest analysis)

### Comments
- Do not write comments in code unless explicitly requested
- Code should be self-documenting through clear naming and structure
- PHPDoc blocks for type hints are acceptable

### Git Commits
- Use Conventional Commits format: `type(scope): description`
- Common types: `feat`, `fix`, `refactor`, `test`, `docs`, `chore`, `style`, `perf`
- Write commit messages in English
- Do not use emojis in commit messages
- Keep commits focused and atomic

### Constructor Pattern
Constructo relies on constructor-based hydration:
```php
class User extends Entity
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly Status $status = Status::ACTIVE,
    ) {}
}
```

### Naming Conventions
- **snake_case**: Used in data arrays (JSON, database)
- **camelCase**: Used in PHP properties
- Automatic conversion via `Notation` class

### Testing
- Test files mirror src/ structure in tests/
- Use `MakeExtension` trait for creating test instances
- Use `BuilderExtension` trait for builder() access
- Use `FakerExtension` trait for fake data generation
- PHPUnit configuration in `phpunit.xml`
- Test results cached in `tests/.phpunit/`

## Common Patterns

### Creating Objects from Data
```php
use Constructo\Core\Serialize\Builder;
use Constructo\Support\Set;

$builder = new Builder();
$user = $builder->build(User::class, Set::createFrom([
    'id' => 1,
    'name' => 'John Doe',
    'status' => 'active',
]));
```

### Converting Objects to Data
```php
use Constructo\Core\Deserialize\Demolisher;

$demolisher = new Demolisher();
$data = $demolisher->demolish($user);
// Returns stdClass with snake_case keys
```

### Custom Formatters
```php
$builder = new Builder(formatters: [
    'string' => fn($val) => strtoupper($val),
    MyClass::class => new MyCustomFormatter(),
]);
```

### Handling Collections
```php
class UserCollection extends Collection implements Collectable
{
    protected function getItemClass(): string
    {
        return User::class;
    }
}

$collection = new UserCollection();
$collection->push($user1);
$demolished = $demolisher->demolishCollection($collection);
```

### Error Handling
```php
use Constructo\Exception\AdapterException;
use Constructo\Support\Datum;

try {
    $result = $builder->build(User::class, $data);
} catch (AdapterException $e) {
    $datum = new Datum($e, $data);
    $errorData = $datum->export(); // Contains '@error' key
}
```

## Dependencies

**Runtime:**
- PHP 8.3+ (strict requirement)
- ext-json
- jawira/case-converter: Case conversion utilities
- visus/cuid2: ID generation
- fakerphp/faker: Test data generation

**Development:**
- PHPUnit 10.5+
- PHPStan 2+ (level 10)
- Rector 2+ (code modernization)
- PHPCS (PSR-12)
- PHPMD, Psalm (additional analysis)

## Maintenance Notes

- Immutability is preferred (Set, Value are readonly)
- Builder and Demolisher are stateless (can be reused)
- Resolver chains execute in specific order (see architecture section)
- Property visibility: prefer public readonly over private with getters
- All autoloaded helper functions check for existence before definition
