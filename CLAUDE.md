# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## General Documentation

See [AGENTS.md](./AGENTS.md) for complete project documentation including:
- Project overview and architecture
- Development commands (testing, linting, CI)
- Code conventions and patterns
- Dependencies and maintenance notes

## Claude Code Specific Instructions

### Code References
When referencing specific functions or code locations, use the pattern `file_path:line_number` to allow easy navigation:

```
The Builder resolves parameters in src/Core/Serialize/Builder.php:84
The Demolisher processes collections in src/Core/Deserialize/Demolisher.php:47
```

### Testing Workflow
When making changes:
1. Run specific tests: `docker-compose exec constructo vendor/bin/phpunit tests/path/to/TestFile.php`
2. Run full test suite: `make test`
3. Run linters: `make lint`
4. Fix auto-fixable issues: `make fix`
5. Run full CI check: `make ci`

For interactive work, use `make bash` to enter the container.

### Docker Environment
All commands must be executed via Makefile (which wraps docker-compose):
- Use `make bash` to enter the container for interactive work
- Container name: `constructo-app`
- Image: `devitools/hyperf:8.3-arm-dev`
- Working directory inside container: `/opt/www`

### Language Context
- README.md is in Portuguese (pt-BR)
- Code and technical documentation should be in English
- When interacting with users, match their language preference

### Code Style
- Do not write comments in code unless explicitly requested by the user
- Code should be self-documenting through clear naming and structure
- PHPDoc blocks for type hints are acceptable and encouraged
