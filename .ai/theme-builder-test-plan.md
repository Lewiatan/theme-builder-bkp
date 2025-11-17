# Test Plan for E-Commerce Theme Builder

## 1. Test Strategy Overview

This test plan outlines the strategy for ensuring the quality and reliability of the **E-Commerce Theme Builder** monorepo. Our approach is based on the testing pyramid, emphasizing a strong foundation of unit tests for each module, complemented by broader integration and end-to-end tests that span the entire application stack.

The primary objectives are:
- To validate the core functionalities of each module (`theme-builder`, `demo-shop`, `backend`).
- To ensure a stable and bug-free user experience for critical user flows.
- To establish an isolated and reliable database environment for automated testing.
- To automate the entire testing process and integrate it into the CI/CD pipeline to catch regressions early.

## 2. Per-Module Unit Testing Strategy

### 2.1. `theme-builder` (React SPA)

- **Objective**: To test individual components, hooks, and utilities of the editor interface in isolation.
- **Tools**: `Vitest`, `@testing-library/react`.
- **Scope**:
  - **UI Components**: Test presentational components (e.g., `TopNavigationBar`, `Button`, `DraggableComponentCard`) for correct rendering based on props and state.
  - **State Management**: Test the `workspaceReducer` in `contexts/WorkspaceContext.tsx` with a variety of actions to ensure predictable and correct state transitions.
  - **Custom Hooks**: Test hooks like `useDragAndDrop` by mocking their dependencies and asserting their output and side effects.
  - **Authentication Utilities**: Validate JWT decoding and data extraction logic in `lib/auth.ts`.
  - **API Clients**: Test functions in `lib/api/pages.ts` to ensure they construct correct API requests. The `fetch` call itself will be mocked.

### 2.2. `demo-shop` (React SSR App)

- **Objective**: To verify the components and data-fetching logic responsible for rendering the public-facing preview of a shop.
- **Tools**: `Vitest`, `@testing-library/react`.
- **Scope**:
  - **Routing**: Test the React Router v7 configuration to ensure routes like `/shop/{shopId}` correctly render the main page component.
  - **Data Fetching**: Test custom hooks responsible for fetching shop-specific data (layouts, themes, products) from the backend. API calls will be mocked using `msw`.
  - **Page Rendering**: Test that page components correctly process fetched layout data and render the corresponding shared components from the `@shared` library.
  - **Shared Component Integration**: Verify that shared components receive the correct props and function as expected within the `demo-shop` environment.

### 2.3. `backend` (Symfony API)

- **Objective**: To test the business logic, data access, and request handling of the Symfony backend API.
- **Tools**: `PHPUnit`.
- **Scope**:
  - **Service Classes**: All business logic encapsulated in `src/Service/` classes should be unit tested. Dependencies (like repositories or the entity manager) will be mocked using PHPUnit's test double capabilities.
  - **Request Validation**: Test custom request DTOs and their associated validation constraints to ensure they correctly validate incoming data and produce appropriate error messages.
  - **Doctrine Repositories**: Test methods containing complex DQL (Doctrine Query Language). For simple CRUD methods, integration tests are more appropriate.
  - **Event Listeners/Subscribers**: Unit test any event listeners to confirm they execute the correct logic when their corresponding event is dispatched.
  - **Data Transformers/Mappers**: Test any classes responsible for transforming data between layers (e.g., Entity to DTO) to ensure data integrity.

## 3. Test Types and Levels (Integration & E2E)

### 3.1. Integration Testing

- **Objective**: To test the interaction between different parts of the application.
- **Scope**:
  - **`theme-builder`**: Test `WorkspaceContext`'s interaction with a mocked API (`msw`) to cover loading, success, and error states. Test the full drag-and-drop flow within a `DndContext` provider.
  - **`backend`**: Test the full flow from a Controller to the database. These tests will use an actual test database (see Section 5) to verify that controllers, services, and repositories work together correctly.
- **Tools**: `Vitest` (Frontend), `PHPUnit` (Backend), `msw`.

### 3.2. End-to-End (E2E) Testing

- **Objective**: To simulate real user scenarios across the entire application stack.
- **Scope**:
  - **Login & Page Building**: Log in, drag a component to the canvas, reorder it, delete it, and save the layout.
  - **Unsaved Changes**: Make a change, attempt to switch pages, and verify the confirmation dialog.
  - **Preview**: Save a layout, then open the `demo-shop` preview URL and verify the changes are rendered correctly.
- **Tools**: `Playwright`.

## 4. Testing Tools and Frameworks

| Tool                      | Purpose                               | Module(s) Used In      |
| ------------------------- | ------------------------------------- | ---------------------- |
| **Vitest**                | JS Unit & Integration Testing         | `theme-builder`, `demo-shop` |
| **React Testing Library** | Component Testing                     | `theme-builder`, `demo-shop` |
| **PHPUnit**               | PHP Unit & Integration Testing        | `backend`              |
| **Playwright**            | E2E & Visual Regression Testing       | All (cross-module)     |
| **MSW (Mock Service Worker)** | Frontend API Mocking                | `theme-builder`, `demo-shop` |
| **ESLint / PHP CS Fixer** | Static Code Analysis                  | All                    |
| **TypeScript / Psalm**    | Static Type Checking                  | All                    |

## 5. Integrated Test Database Strategy

To ensure tests are reliable and isolated without complicating the local development workflow, a separate but integrated PostgreSQL database will be used for all automated testing. This approach makes the test database readily available, allowing developers to run tests at any time without managing a separate environment.

- **1. Enhance `docker-compose.yml`**:
  - A new service named `postgres_test` will be added directly to the main `docker-compose.yml` file, alongside the existing `postgres` service.
  - **Configuration for `postgres_test`**:
    - **Port Mapping**: It will map the container's port 5432 to a different host port (e.g., `5433:5432`) to prevent collision with the development database.
    - **Volume**: It will use a dedicated named volume (e.g., `postgres_test_data`) to keep test data completely separate from development data.
    - **Environment**: It will use a distinct set of environment variables for the database name, user, and password (e.g., `POSTGRES_DB_TEST`, `POSTGRES_USER_TEST`).

- **2. Environment Variable Configuration**:
  - The `.env.example` file will be updated with the new variables required for the test database (e.g., `POSTGRES_TEST_DB`, `DATABASE_TEST_URL`).
  - Developers will copy these into their local `.env` file, allowing Docker Compose to configure the `postgres_test` service automatically.

- **3. Backend Test Configuration**:
  - The `backend/.env.test` file will be configured with a `DATABASE_URL` that points to the `postgres_test` service name (e.g., `pgsql://test_user:test_pass@postgres_test:5432/test_db`).
  - When tests are run, Symfony's `APP_ENV=test` setting will ensure all database operations are automatically routed to the isolated test database.

- **4. Developer Workflow**:
  1.  Run `docker compose up -d`. This single command will start all services, including both the development `postgres` and the `postgres_test` databases.
  2.  To run backend tests, the developer can simply execute the test script (e.g., `docker compose exec backend composer test`), which will handle schema migration, data seeding, and running PHPUnit against the always-on test database.

- **5. CI/CD Workflow**:
  - The CI pipeline will also run `docker compose up -d` to start the entire environment.
  - A dedicated test script will then be executed, which will first wipe the test database schema, run all migrations and seeders to ensure a clean and consistent state, and then execute the full PHPUnit and Playwright test suites.
  - Finally, `docker compose down -v` will tear down the environment and remove the volumes, ensuring each CI run is completely stateless.

## 6. Automation Strategy

- **Continuous Integration**: A GitHub Actions workflow will run on every pull request. A PR will be blocked from merging if any linting, type-checking, or test step fails.
- **Test Scripts**: `package.json` and `composer.json` will contain scripts to run tests for each module (e.g., `npm test`, `composer test`) and a root-level script to orchestrate all checks for the CI pipeline.

## 7. Risk Assessment

| Risk                                  | Mitigation Strategy                                                                                             | Priority |
| ------------------------------------- | --------------------------------------------------------------------------------------------------------------- | -------- |
| **Inconsistent Test Environments**    | A fully containerized test database and environment defined in code (`docker-compose.test.yml`) will be used.   | High     |
| **Drag-and-Drop Failures**            | Thorough integration testing of `useDragAndDrop` hook and E2E tests covering all drag-and-drop scenarios.         | High     |
| **Incorrect State Management**        | Unit tests for the `workspaceReducer`, and integration tests for the `WorkspaceContext` with mocked API calls.    | High     |
| **API Integration Issues**            | Backend integration tests using the test database; frontend integration tests using `msw`.                        | High     |

## 8. Success Criteria

- **Code Coverage**: A target of **80%** combined coverage for unit and integration tests across all modules.
- **E2E Test Stability**: A consistent pass rate of **>98%** for the E2E test suite in the CI pipeline.
- **Bug Detection**: A downward trend in bugs reported from manual testing and a low number of regressions found in production.
- **CI/CD Integration**: All pull requests must pass the full suite of automated checks before being eligible for merging.