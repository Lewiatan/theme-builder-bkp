# CLAUDE.md

Follow AGENTS.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

E-commerce Theme Builder is an MVP application that enables non-technical shop owners to create and customize their online store appearance through a visual drag-and-drop interface. The project consists of three main components in a monorepo structure:

1. **theme-builder** - The main editor application (React + Vite)
2. **demo-shop** - Preview/demo store (React Router 7 + SSR)
3. **backend** - API server (Symfony 7.3 + PHP 8.2)

The architecture has recently transitioned from Supabase BaaS to a custom Symfony PHP backend with PostgreSQL, as indicated by the `switch-to-php-api` branch.

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
# Run inside backend container or locally with PHP 8.2+
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

## Architecture & Key Concepts

### Monorepo Structure

The repository follows a monorepo pattern with three independent applications:

- **theme-builder/**: Simple React + Vite app for the visual editor interface
- **demo-shop/**: React Router 7 SSR application for rendering the themed store
- **backend/**: Symfony 7.3 API with minimal setup (no ORM configured yet)

Each application has its own dependencies managed separately. Docker Compose orchestrates all services with shared networking.

### Backend Architecture (Symfony)

The backend is a minimal Symfony 7.3 installation:

- **Framework**: Symfony 7.3 with PHP 8.2 (via php:8.2-fpm Docker image)
- **Database**: PostgreSQL 16 (accessed via PDO extensions)
- **Migrations**: Phinx (robmorgan/phinx) - NOT Doctrine migrations
- **Server**: Nginx proxying to PHP-FPM
- **No ORM**: Direct database access using Symfony components

The backend structure follows standard Symfony conventions:
- `src/Controller/` - API controllers
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
- User authentication via Supabase (now likely migrating to Symfony)
- Image storage via Cloudflare R2
- Edge functions via Cloudflare Workers

Current state (switch-to-php-api branch):
- Backend handles API requests via Symfony controllers
- PostgreSQL stores application data
- Frontend apps communicate with backend via `VITE_API_URL` environment variable

## Key Features & Scope

### MVP Features (from README)

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
- **Configuration**: Phinx config location needs to be determined (not in standard location)
- **Connection**: Managed via Docker Compose networking (`theme-builder-network`)
- **Admin UI**: pgAdmin available at http://localhost:5050

## Development Workflow

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

## Coding Standards

Refer to AGENTS.md for comprehensive guidelines including:

- **React**: Use functional components, hooks, React.memo, lazy loading, useCallback/useMemo
- **React Router**: Use createBrowserRouter, loader/action functions, error boundaries
- **Tailwind**: JIT mode, @layer directive, component extraction for repeated patterns
- **TypeScript**: Strict typing, type-level assertions
- **Testing**: Vitest for unit tests, Playwright for E2E (when implemented)
- **Git**: Conventional commits, meaningful commit messages, feature branches
- **Architecture**: Clean architecture principles, monorepo best practices

## Common Gotchas

1. **Node Modules in Docker**: Each frontend service uses Docker volumes for node_modules to avoid platform compatibility issues
2. **Phinx Location**: Migrations tool is installed but configuration file location is non-standard
3. **No TypeScript in theme-builder**: The main editor uses vanilla JavaScript, while demo-shop uses TypeScript
4. **Port Confusion**: Demo shop runs on 5174 (not 5173) to avoid conflicts
5. **Backend Entrypoint**: The docker-entrypoint.sh automatically runs `composer install` on container start
