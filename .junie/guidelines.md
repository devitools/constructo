# Constructo Development Guidelines

## Build/Configuration Instructions

### Environment Setup

This project uses **Docker Compose** for development environment management. All development commands must be executed
within Docker containers.

**Prerequisites:**

- Docker and Docker Compose installed
- PHP 8.3+ (handled by Docker)

**Initial Setup:**

```bash
make setup  # Prunes, installs dependencies, and starts containers
```

**Key Commands:**

```bash
make up     # Start Docker containers
make down   # Stop containers
make install # Install Composer dependencies
```

### Composer Scripts

The project includes comprehensive Composer scripts for development:

```bash
# Via Docker (recommended)
make test           # Run PHPUnit tests with coverage
make lint           # Run all linting tools
make ci             # Full CI pipeline (lint + test)

# Individual linting tools
make lint-phpcs     # PHP CodeSniffer (PSR-12)
make lint-phpstan   # Static analysis
make lint-phpmd     # Mess detector
make lint-rector    # Code modernization
make lint-psalm     # Static analysis
```

## Testing Information

### Test Configuration

- **Framework:** PHPUnit 10.5+
- **Configuration:** `phpunit.xml`
- **Test Directory:** `tests/`
- **Coverage Reports:** Generated in `tests/.phpunit/` (HTML, Clover, Text formats)
- **Extensions:** PHPUnit Pretty Print with profiling and memory display

### Running Tests

```bash
# Run all tests with coverage
make test

# Run specific test file (within Docker container)
docker compose exec app vendor/bin/phpunit tests/Path/To/TestFile.php
```

### Test Structure and Patterns

Tests follow the src/ directory structure and use these patterns:

1. **Namespace:** `Constructo\Test\` + corresponding src namespace
2. **Class naming:** `{ClassName}Test extends TestCase`
3. **Method naming:** `test{FeatureDescription}(): void`

**Example Test Pattern:**

```php
<?php
declare(strict_types=1);

namespace Constructo\Test\Core\Example;

use Constructo\Core\Serialize\Builder;
use Constructo\Support\Set;
use PHPUnit\Framework\TestCase;

final class ExampleTest extends TestCase
{
    public function testBasicFunctionality(): void
    {
        $builder = new Builder();
        $data = Set::createFrom(['key' => 'value']);
        
        $result = $builder->build(\stdClass::class, $data);
        
        $this->assertInstanceOf(\stdClass::class, $result);
    }
}
```

### Adding New Tests

1. Create a test file in `tests/` mirroring the `src/` structure
2. Use `final class {Name}Test extends TestCase`
3. Import required classes and use stub classes from `Constructo\Test\Stub\`
4. Use `Set::createFrom()` for test data and `Target::createFrom()` for reflection
5. Run tests via `make test` to verify

### Test Best Practices

**Avoid Redundant Type Checking:**

- Do not use `$this->assertInstanceOf(ClassName::class, $object)` immediately after creating an object with
  `new ClassName()` or calling a method that returns a specific type
- If the constructor or method fails, PHP will throw an exception or type error; the test doesn't need to verify the
  object type
- Instead, test meaningful functionality like method calls, state changes, return values, or object behavior
- **Bad Example:** `$registry = $factory->make(); $this->assertInstanceOf(Registry::class, $registry);`
- **Good Example:** `$registry = $factory->make(); $this->assertTrue($registry->hasSpec('required'));`
- Focus on testing that the created object works correctly, not just that it exists

## Additional Development Information

### Project Architecture

**Constructo** is a PHP serialization/deserialization library with these core components:

- **Contracts:** Interfaces defining library behavior (`src/Contract/`)
- **Core:** Main serialization/deserialization logic (`src/Core/`)
- **Support:** Utility classes and reflection helpers (`src/Support/`)
- **Types:** Type-specific implementations (`src/Type/`)

## Coding Standards and Practices

This project adheres to strict coding standards and best practices:

- **Strict Types:** All files use `declare(strict_types=1);`
- **PSR-12:** Follow PSR-12 coding standards for consistency
- **Type Hints:** Just use type hints if the typing is not clear or is ambiguous
- **Comments:** Do not use comments for code explanations, prefer clear code structure

### Key Classes

- `Builder`: Main deserialization class using chain of responsibility pattern
- `Set`: Data container for serialization input
- `Target`: Reflection wrapper for class analysis
- `Engine`: Base class for reflection-based operations

### Code Quality Tools

The project enforces strict code quality:

- **PHP 8.3+ strict types** (`declare(strict_types=1);`)
- **PSR-12 coding standards** (PHP CodeSniffer)
- **Static analysis** (PHPStan level max, Psalm)
- **Code modernization** (Rector)
- **Mess detection** (PHPMD)

### Autoloading

- **PSR-4:** `Constructo\` → `src/`
- **Functions:** Auto-loaded from `functions/` directory
- **Test PSR-4:** `Constructo\Test\` → `tests/`

### Dependencies

- **Runtime:** PHP 8.3+, ext-json, jawira/case-converter, visus/cuid2
- **Testing:** PHPUnit, Faker, php-mock
- **Quality:** Multiple static analysis and linting tools

### Development Workflow

1. Use Docker environment for all operations
2. Follow strict typing and PSR-12 standards
3. Write comprehensive tests with stub classes
4. Run a full CI pipeline before commits: `make ci`
5. Use `make fix` for automated code formatting
