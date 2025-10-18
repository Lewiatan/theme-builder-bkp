### Backend Architecture (Symfony)

The backend is a Symfony 7.3 installation with Doctrine ORM:

- **Framework**: Symfony 7.3 with PHP 8.3 (via php:8.3-fpm Docker image)
- **Database**: PostgreSQL 16 (accessed via Doctrine ORM)
- **Migrations**: Phinx (robmorgan/phinx) - NOT Doctrine migrations
- **Server**: Nginx proxying to PHP-FPM

The backend structure follows standard Symfony conventions:
- `src/Controller/` - API controllers
- `src/Model/` - Application model
- `src/Repository/` - Database repositories
- `src/Service/` - Business logic services
- `src/Security/` - Authentication & authorization
- `src/Kernel.php` - Application kernel
- `config/` - Configuration files (services, routes, packages)
- `bin/console` - Symfony console commands
- `public/index.php` - Entry point

### Backend Standards

#### PHP

**PHP Version**: 8.3+ features are encouraged

- Use constructor property promotion to reduce boilerplate code in classes
- Leverage readonly properties for immutable value objects and entities
- Always use strict typing with `declare(strict_types=1)` at the top of every file
- Use typed properties for all class properties (prefer strict types over mixed)
- Implement named arguments for better readability when calling functions with many parameters
- Use PHP 8.1+ enums for fixed sets of values instead of class constants
- Extract validation logic into private methods to avoid duplication
- Use class constants for configuration values (e.g., MAX_LENGTH) instead of magic numbers
- Prefer final classes by default to encourage composition over inheritance
- Use null coalescing operator (??) and nullsafe operator (?->) for concise null handling

#### Symfony

- Follow Symfony best practices and conventions for directory structure
- Use Symfony's dependency injection container for service management
- Implement custom console commands for administrative tasks
- Use Symfony's validation component for input validation in controllers
- Leverage Symfony's security component for authentication and authorization
- Configure services in YAML files under config/services.yaml
- Use environment variables for configuration with proper .env files
- Implement custom event listeners for cross-cutting concerns

#### ReadModel Pattern

- Use ReadModels to decouple API responses from domain entities
- Create ReadModels in `src/ReadModel/` directory
- ReadModels should be `final readonly class` for immutability
- Implement `\JsonSerializable` for automatic JSON conversion
- Create ReadModels in the repository layer, not in services or controllers
- Only include fields intended for public API exposure in ReadModels
- Never include sensitive data (timestamps, IDs, related entities) unless explicitly needed
- Use ReadModels to prevent accidental exposure of entity data through serialization

**Benefits**:
- Explicit data contract - only specified fields can be returned
- Decouples API response structure from domain entity changes
- Prevents accidental data exposure when entities evolve
- No risk of serialization misconfiguration exposing sensitive data
- Repository returns ReadModel directly, eliminating transformation logic in services/controllers

### Database Standards

#### PostgreSQL

- Use connection pooling to manage database connections efficiently
- Implement JSONB columns for semi-structured data instead of creating many tables for flexible data
- Use materialized views for complex, frequently accessed read-only data

**Migration Tool**: Phinx (NOT Doctrine migrations)

### Testing Standards

#### PHPUnit (Unit Testing)
- run tests via Docker container
