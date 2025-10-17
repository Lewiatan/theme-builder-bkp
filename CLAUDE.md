# CLAUDE.md

This file provides comprehensive guidance to Claude Code when working with code in this repository.

## Project Overview

E-commerce Theme Builder is an MVP application that enables non-technical shop owners to create and customize their online store appearance through a visual drag-and-drop interface. The project consists of three main components in a monorepo structure:

1. **theme-builder** - The main editor application (React + Vite)
2. **demo-shop** - Preview/demo store (React Router 7 + SSR)
3. **backend** - API server (Symfony 7.3 + PHP 8.3)

This application serves as both a **10xDevs certification project** (demonstrating proficiency in AI-Assisted Development techniques) and the foundational element for a future **E-commerce SaaS** platform.

## AI Assistant Guidelines

When providing code assistance:

- Favor elegant, maintainable solutions over verbose code. Assume understanding of language idioms and design patterns.
- Highlight potential performance implications and optimization opportunities in suggested code.
- Frame solutions within broader architectural contexts and suggest design alternatives when appropriate.
- Focus comments on 'why' not 'what' - assume code readability through well-named functions and variables.
- Proactively address edge cases, race conditions, and security considerations without being prompted.
- When debugging, provide targeted diagnostic approaches rather than shotgun solutions.
- Suggest comprehensive testing strategies rather than just example tests, including considerations for mocking, test organization, and coverage.

## Development Environment

### Starting the Development Environment

```bash
# Start all services with Docker Compose
docker-compose up

# Services will be available at:
# - Theme Builder: http://localhost:5173
# - Demo Shop: http://localhost:5174
# - Backend API: http://localhost:8000
# - pgAdmin: http://localhost:5050
# - PostgreSQL: localhost:5432
```

### Individual Service Commands

**Backend (Symfony):**
```bash
# Run inside backend container or locally with PHP 8.3+
composer install                    # Install dependencies
php bin/console cache:clear         # Clear Symfony cache
php bin/console debug:router        # List all routes

# Database migrations (using Phinx)
vendor/bin/phinx migrate           # Run migrations
vendor/bin/phinx create MigrationName  # Create new migration
```

**Theme Builder:**
```bash
cd theme-builder
npm install
npm run dev        # Start dev server
npm run build      # Build for production
npm run lint       # Run ESLint
npm run preview    # Preview production build
```

**Demo Shop:**
```bash
cd demo-shop
npm install
npm run dev        # Start dev server with React Router
npm run build      # Build for production
npm run start      # Serve production build
npm run typecheck  # Run TypeScript type checking and generate React Router types
```

### Environment Variables

Copy `.env.example` to `.env` and configure:
- PostgreSQL credentials (POSTGRES_DB, POSTGRES_USER, POSTGRES_PASSWORD)
- pgAdmin credentials
- Backend settings (APP_ENV, APP_SECRET, DATABASE_URL)
- Frontend API URL (VITE_API_URL)

### Docker Compose Services

All services are orchestrated via `docker-compose.yml`:
- **postgres**: PostgreSQL 16 database with healthcheck
- **pgadmin**: Database management UI
- **backend**: Symfony PHP-FPM application
- **nginx**: Web server for backend API
- **theme-builder**: React + Vite dev server (Node 20)
- **demo-shop**: React Router + Vite dev server (Node 20)

Volumes persist data for postgres, pgadmin, and node_modules.

## Architecture & Key Concepts

### Monorepo Structure

The repository follows a monorepo pattern with three independent applications:

- **theme-builder/**: Simple React + Vite app for the visual editor interface
- **demo-shop/**: React Router 7 SSR application for rendering the themed store
- **backend/**: Symfony 7.3 API with Doctrine ORM

Each application has its own dependencies managed separately. Docker Compose orchestrates all services with shared networking.

**Monorepo Best Practices:**
- Configure workspace-aware tooling to optimize build and test processes
- Implement clear package boundaries with explicit dependencies between packages
- Use consistent versioning strategy across all packages (independent or lockstep)
- Configure CI/CD to build and test only affected packages for efficiency
- Implement shared configurations for linting, testing, and development tooling
- Use code generators to maintain consistency across similar packages or modules

### Clean Architecture Principles

- Strictly separate code into layers: entities, use cases, interfaces, and frameworks
- Ensure dependencies point inward, with inner layers having no knowledge of outer layers
- Implement domain entities that encapsulate business rules without framework dependencies
- Use interfaces (ports) and implementations (adapters) to isolate external dependencies
- Create use cases that orchestrate entity interactions for specific business operations
- Implement mappers to transform data between layers to maintain separation of concerns

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

### Frontend Architecture

**Theme Builder:**
- Vanilla React 19 with Vite for HMR
- No routing library (simple SPA)
- Expected to have drag-and-drop functionality (dnd kit mentioned in main README)
- Component-based architecture for 13 predefined components

**Demo Shop:**
- React Router 7 with SSR capabilities
- Tailwind CSS 4 for styling
- Uses React Router's file-based routing (`app/routes/`)
- Designed to render saved themes with mock product data

### Data Flow

The original architecture planned:
- User authentication via JWT with LexikJWTAuthenticationBundle
- Image storage via Cloudflare R2
- Edge functions via Cloudflare Workers

Current state (switch-to-php-api branch):
- Backend handles API requests via Symfony controllers
- PostgreSQL stores application data via Doctrine ORM
- Frontend apps communicate with backend via `VITE_API_URL` environment variable

## Coding Standards

### General Practices

**Documentation:**
- Keep README.md in sync with new capabilities
- Maintain changelog entries in CHANGELOG.md
- Kepp CLAUDE.md in sync with new capabilities

**Static Analysis:**
- Configure project-specific rules in eslint.config.js to enforce consistent coding standards
- Use shareable configs like eslint-config-airbnb or eslint-config-standard as a foundation
- Configure integration with Prettier to avoid rule conflicts for code formatting
- Use the --fix flag in CI/CD pipelines to automatically correct fixable issues
- Implement staged linting with husky and lint-staged to prevent committing non-compliant code

### Frontend Standards

#### React

- Use functional components with hooks instead of class components
- Implement React.memo() for expensive components that render often with the same props
- Utilize React.lazy() and Suspense for code-splitting and performance optimization
- Use the useCallback hook for event handlers passed to child components to prevent unnecessary re-renders
- Prefer useMemo for expensive calculations to avoid recomputation on every render
- Implement useId() for generating unique IDs for accessibility attributes
- Use the new use hook for data fetching in React 19+ projects
- Consider using the new useOptimistic hook for optimistic UI updates in forms
- Use useTransition for non-urgent state updates to keep the UI responsive

#### React Router

- Use createBrowserRouter instead of BrowserRouter for better data loading and error handling
- Implement lazy loading with React.lazy() for route components to improve initial load time
- Use the useNavigate hook instead of the navigate component prop for programmatic navigation
- Leverage loader and action functions to handle data fetching and mutations at the route level
- Implement error boundaries with errorElement to gracefully handle routing and data errors
- Use relative paths with dot notation (e.g., "../parent") to maintain route hierarchy flexibility
- Utilize the useRouteLoaderData hook to access data from parent routes
- Implement fetchers for non-navigation data mutations
- Use route.lazy() for route-level code splitting with automatic loading states
- Implement shouldRevalidate functions to control when data revalidation happens after navigation

#### Tailwind CSS

- Use the @layer directive to organize styles into components, utilities, and base layers
- Implement Just-in-Time (JIT) mode for development efficiency and smaller CSS bundles
- Use arbitrary values with square brackets (e.g., w-[123px]) for precise one-off designs
- Leverage the @apply directive in component classes to reuse utility combinations
- Implement the Tailwind configuration file for customizing theme, plugins, and variants
- Use component extraction for repeated UI patterns instead of copying utility classes
- Leverage the theme() function in CSS for accessing Tailwind theme values
- Implement dark mode with the dark: variant
- Use responsive variants (sm:, md:, lg:, etc.) for adaptive designs
- Leverage state variants (hover:, focus:, active:, etc.) for interactive elements

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

### Database Standards

#### PostgreSQL

- Use connection pooling to manage database connections efficiently
- Implement JSONB columns for semi-structured data instead of creating many tables for flexible data
- Use materialized views for complex, frequently accessed read-only data

**Migration Tool**: Phinx (NOT Doctrine migrations)

### Testing Standards

#### Vitest (Unit Testing)

- Leverage the `vi` object for test doubles - Use `vi.fn()` for function mocks, `vi.spyOn()` to monitor existing functions, and `vi.stubGlobal()` for global mocks
- Master `vi.mock()` factory patterns - Place mock factory functions at the top level of your test file
- Create setup files for reusable configuration - Define global mocks, custom matchers, and environment setup in dedicated files referenced in your `vitest.config.ts`
- Use inline snapshots for readable assertions with `expect(value).toMatchInlineSnapshot()`
- Monitor coverage with purpose and only when asked - Configure coverage thresholds in `vitest.config.ts`
- Make watch mode part of your workflow - Run `vitest --watch` during development
- Explore UI mode for complex test suites - Use `vitest --ui` to visually navigate large test suites
- Configure jsdom for DOM testing - Set `environment: 'jsdom'` for frontend component tests
- Structure tests for maintainability - Group related tests with descriptive `describe` blocks
- Leverage TypeScript type checking in tests - Enable strict typing, use `expectTypeOf()` for type-level assertions

#### Playwright (E2E Testing)

- Initialize configuration only with Chromium/Desktop Chrome browser
- Use browser contexts for isolating test environments
- Implement the Page Object Model for maintainable tests
- Use locators for resilient element selection
- Leverage API testing for backend validation
- Implement visual comparison with expect(page).toHaveScreenshot()
- Use the codegen tool for test recording
- Leverage trace viewer for debugging test failures
- Implement test hooks for setup and teardown
- Use expect assertions with specific matchers
- Leverage parallel execution for faster test runs

### Version Control

#### Git

- Use conventional commits to create meaningful commit messages
- Use feature branches with descriptive names
- Write meaningful commit messages that explain why changes were made, not just what
- Keep commits focused on single logical changes to facilitate code review and bisection
- Use interactive rebase to clean up history before merging feature branches
- Leverage git hooks to enforce code quality checks before commits and pushes

### DevOps

#### GitHub Actions

- Check if `package.json` exists in project root and summarize key scripts
- Check if `.nvmrc` exists in project root
- Check if `.env.example` exists in project root to identify key `env:` variables
- Always use terminal command: `git branch -a | cat` to verify whether we use `main` or `master` branch
- Always use `env:` variables and secrets attached to jobs instead of global workflows
- Always use `npm ci` for Node-based dependency setup
- Extract common steps into composite actions in separate files
- For each public action always verify the most up-to-date version (use only major version)

## Key Features & Scope

### MVP Features

- Drag-and-drop interface for managing components
- 13 predefined components with variants
- User authentication and isolated data
- 4 default pages (Home, Catalog, Product, Contact)
- Global theme customization (colors, fonts)
- Separate save mechanisms for layouts and theme settings
- Unsaved changes protection
- Demo store preview with mock data
- Fully responsive design

### Explicitly Excluded from MVP

- Forgot password functionality
- Global media library
- Global Header/Footer components
- Undo/Redo
- Auto-saving
- Custom component creation
- Viewport switcher in editor
- Real product database integration
- Server-Side Rendering (SSR) for main app
- Template versioning

## Database

- **Type**: PostgreSQL 16
- **Migration Tool**: Phinx (NOT Doctrine)
- **ORM**: Doctrine ORM for data access
- **Connection**: Managed via Docker Compose networking (`theme-builder-network`)
- **Admin UI**: pgAdmin available at http://localhost:5050

## Common Gotchas

1. **Node Modules in Docker**: Each frontend service uses Docker volumes for node_modules to avoid platform compatibility issues
2. **Phinx Location**: Migrations tool is Phinx (robmorgan/phinx), not Doctrine migrations
3. **No TypeScript in theme-builder**: The main editor uses vanilla JavaScript, while demo-shop uses TypeScript
4. **Port Confusion**: Demo shop runs on 5174 (not 5173) to avoid conflicts
5. **Backend Entrypoint**: The entrypoint.sh automatically runs `composer install` on container start
6. **Docker Files Organization**: Docker-related files are in `backend/docker/` directory (nginx.conf, entrypoint.sh)
- Use "docker compose" instead of "docker-compose"