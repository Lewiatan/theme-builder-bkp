# View Implementation Plan: Demo Shop Home Page

## 1. Overview

The Demo Shop Home Page is the primary entry point for customers viewing a published e-commerce store. It fetches and renders saved page layout configuration from the backend API, applies global theme settings, and displays components dynamically using the shared component registry. The page is built using React Router 7 with server-side rendering capabilities and implements loader-based data fetching for optimal performance.

## 2. View Routing

**Primary Route:** `/shop/:shopId`

This route serves as the home page for the Demo Shop. The `shopId` parameter identifies which shop's configuration should be loaded and rendered. This route will be defined in `app/routes.ts` using React Router's route configuration API.

## 3. Component Structure

```
ShopHomeRoute (route component)
├── ShopLayout (layout wrapper)
│   └── DynamicComponentRenderer (layout renderer)
│       ├── HeaderNavigation (from shared components)
│       ├── Heading (from shared components)
│       ├── TextSection (from shared components)
│       └── ... (additional shared components as configured)
└── ErrorBoundary (route-level error handling)
```

## 4. Component Details

### ShopHomeRoute

- **Component description:** Top-level route component that orchestrates data loading via React Router loader and renders the shop layout. Handles loading states and provides data to child components through React Router's data loading mechanisms.

- **Main elements:**
  - Root `<div>` container for the entire page
  - `DynamicComponentRenderer` component that receives layout data
  - Loading indicator during data fetch
  - Error boundary fallback UI

- **Handled events:**
  - None (purely presentational)

- **Validation conditions:**
  - Validates that `shopId` parameter exists and is a valid UUID format
  - Ensures loader data is available before rendering
  - Handles cases where shop or page data is not found

- **Types:**
  - `ShopHomeLoaderData` (custom ViewModel)
  - `PageLayoutData` (DTO from API)
  - `ThemeSettings` (DTO from API)

- **Props:**
  - None (route component receives data from loader via `useLoaderData()`)

### DynamicComponentRenderer

- **Component description:** Core rendering engine that maps JSON layout configuration to React components. Iterates through the layout's component array and dynamically renders each component from the shared component registry based on component type identifiers.

- **Main elements:**
  - Wrapper `<div>` or `<Fragment>` for component list
  - Mapped array of shared components (HeaderNavigation, Heading, TextSection, etc.)
  - Each component receives validated props from layout data

- **Handled events:**
  - None (purely presentational)

- **Validation conditions:**
  - Validates each component configuration against its Zod schema before rendering
  - Handles missing or invalid component types gracefully
  - Ensures required props are present for each component
  - Validates image URLs are HTTPS
  - Checks color format compliance (hex, rgba)

- **Types:**
  - `ComponentConfig` (DTO representing single component configuration)
  - `ComponentRegistry` (map of component type to React component)
  - Individual component prop types (HeaderNavigationProps, HeadingProps, TextSectionProps, etc.)

- **Props:**
  - `layout: PageLayoutData` - Complete layout configuration from API
  - `themeSettings: ThemeSettings` - Global theme configuration

### ErrorBoundary

- **Component description:** Route-level error boundary that catches rendering errors, loader errors, and HTTP errors. Displays user-friendly error messages with appropriate status codes and allows recovery actions.

- **Main elements:**
  - Error message container `<div>`
  - Error title heading
  - Error details paragraph
  - Optional stack trace (development only)
  - Link to return to a safe state

- **Handled events:**
  - None (displays error state)

- **Validation conditions:**
  - Distinguishes between 404 errors (shop not found) and 500 errors (server errors)
  - Shows appropriate messaging based on error type

- **Types:**
  - `Route.ErrorBoundaryProps` (React Router type)

- **Props:**
  - `error: unknown` - Error object from React Router

## 5. Types

### ShopHomeLoaderData (ViewModel)

Custom view model that aggregates data from multiple API endpoints for efficient rendering.

```typescript
interface ShopHomeLoaderData {
  shopId: string;
  page: PageLayoutData;
  theme: ThemeSettings;
}
```

**Field breakdown:**
- `shopId: string` - UUID of the shop, extracted from route params
- `page: PageLayoutData` - Page layout configuration received from backend
- `theme: ThemeSettings` - Global theme settings (colors, fonts) for the shop

### PageLayoutData (DTO from API)

Represents the page layout structure returned from `/api/public/shops/{shopId}/pages/home` endpoint.

```typescript
interface PageLayoutData {
  type: string;
  layout: {
    components: ComponentConfig[];
  };
}
```

**Field breakdown:**
- `type: string` - Page type identifier ('home', 'catalog', 'product', 'contact')
- `layout.components: ComponentConfig[]` - Array of component configurations

### ComponentConfig (DTO)

Generic structure for a single component's configuration in the layout.

```typescript
interface ComponentConfig {
  id: string;
  type: string;
  variant: string;
  props: Record<string, any>;
}
```

**Field breakdown:**
- `id: string` - Unique identifier for the component instance
- `type: string` - Component type identifier (maps to shared component registry)
- `variant: string` - Selected variant of the component
- `props: Record<string, any>` - Component-specific props (validated against Zod schema)

### ThemeSettings (DTO)

Global theme configuration applied across all components.

```typescript
interface ThemeSettings {
  colors: {
    primary: string;
    secondary: string;
    background: string;
    text: string;
  };
  fonts: {
    heading: string;
    body: string;
  };
}
```

**Field breakdown:**
- `colors.primary: string` - Primary brand color (hex format)
- `colors.secondary: string` - Secondary accent color (hex format)
- `colors.background: string` - Default background color (hex format)
- `colors.text: string` - Default text color (hex format)
- `fonts.heading: string` - Font family for headings
- `fonts.body: string` - Font family for body text

### ComponentRegistry (Type)

Maps component type strings to React component references.

```typescript
type ComponentRegistry = Record<string, React.ComponentType<any>>;
```

This registry enables dynamic component rendering based on layout configuration.

## 6. State Management

State management for the home view is handled primarily through React Router's built-in data loading mechanisms, eliminating the need for complex client-side state management.

**Loader-Based Data Fetching:**
- React Router's `loader` function fetches all required data (page layout, theme settings) before component rendering
- Data is provided to components via `useLoaderData()` hook
- Automatic error handling through React Router's error boundaries
- Built-in loading states during navigation

**No Custom Hook Required:**
The view does not require a custom state management hook because:
- All data is fetched server-side via loaders
- No client-side mutations occur on this view (read-only)
- Theme settings are applied via CSS custom properties, not React state
- Component configurations are static once loaded

**Theme Application Strategy:**
- Theme settings from the loader are applied to CSS custom properties on the document root
- Uses `useEffect` in the route component to inject theme CSS variables
- This allows all shared components to reference theme colors/fonts via CSS variables

**Error State:**
- Handled automatically by React Router's error boundary mechanism
- Loader errors (network failures, 404s, 500s) are caught and rendered by ErrorBoundary component

## 7. API Integration

### Endpoint: Get Shop Page

**URL:** `GET /api/public/shops/{shopId}/pages/{type}`

**Request:**
- Method: GET
- Path Parameters:
  - `shopId: string` - UUID of the shop (from route params)
  - `type: string` - Page type, defaults to 'home'
- Query Parameters: None
- Headers: None (public endpoint, no authentication required)

**Response:**
- Success (200):
  ```typescript
  {
    type: string;
    layout: {
      components: ComponentConfig[];
    };
  }
  ```
- Not Found (404):
  ```typescript
  {
    error: string; // "Page not found for this shop"
  }
  ```
- Server Error (500):
  ```typescript
  {
    error: string; // "An unexpected error occurred"
  }
  ```

**Implementation in Loader:**
```typescript
export async function loader({ params }: Route.LoaderArgs) {
  const { shopId } = params;

  // Validate shopId format
  if (!isValidUuid(shopId)) {
    throw new Response("Invalid shop ID", { status: 400 });
  }

  // Fetch page data
  const response = await fetch(
    `${API_URL}/api/public/shops/${shopId}/pages/home`
  );

  if (!response.ok) {
    if (response.status === 404) {
      throw new Response("Shop not found", { status: 404 });
    }
    throw new Response("Failed to load shop data", { status: 500 });
  }

  const pageData: PageLayoutData = await response.json();

  // Return aggregated loader data
  return {
    shopId,
    page: pageData,
    theme: {}, // TODO: Fetch theme settings in future iteration
  };
}
```

**Environment Configuration:**
- API base URL is configured via `VITE_API_URL` environment variable
- Loader function reads this from `import.meta.env.VITE_API_URL`

**Error Handling:**
- Network errors throw Response objects with appropriate status codes
- React Router automatically catches thrown responses and renders ErrorBoundary
- Validation errors (invalid UUID) return 400 Bad Request
- Missing resources return 404 Not Found
- Server failures return 500 Internal Server Error

## 8. User Interactions

The home page is primarily a read-only view with minimal direct user interactions. User interactions are limited to navigation and error recovery.

**Navigation:**
- Users can click links within HeaderNavigation component to navigate to other pages (Catalog, Product, Contact)
- Navigation is handled by React Router's `<Link>` component within the HeaderNavigation shared component
- Clicking a navigation link triggers React Router's loader for the target route

**Image Loading:**
- Images in components (Heading backgrounds, TextSection images, HeaderNavigation logo) are loaded asynchronously
- Browser handles image loading states natively
- Failed image loads fall back to alt text or placeholder

**Error Recovery:**
- If ErrorBoundary is displayed (due to 404 or 500 error), user can:
  - See clear error message explaining what went wrong
  - Click a "Return Home" link (if implemented) to navigate away
  - Use browser back button to return to previous page

**Scrolling:**
- If HeaderNavigation uses 'sticky' variant, header remains visible during scroll
- Smooth scrolling behavior is applied via CSS
- No JavaScript scroll listeners required

**Theme Visibility:**
- Global theme settings (colors, fonts) are visible immediately on page load
- No user interaction required to view theme effects
- Theme is applied via CSS custom properties, ensuring consistency across all components

## 9. Conditions and Validation

### Loader-Level Validation

**Shop ID Validation:**
- Condition: `shopId` parameter must be a valid UUID v4 format
- Validation method: Regex test or UUID library validation function
- Impact: If invalid, loader throws 400 Bad Request
- UI effect: ErrorBoundary displays "Invalid shop ID" message

**API Response Validation:**
- Condition: API must return 200 status with valid JSON structure
- Validation method: Check `response.ok` and parse JSON
- Impact: If 404, throw Not Found; if 500, throw Server Error
- UI effect: ErrorBoundary displays appropriate error message

### Component-Level Validation

**Component Configuration Validation:**
- Condition: Each component in layout must have valid type, variant, and props
- Validation method: Zod schema validation for each component type
- Impact: If invalid, component is skipped or fallback is rendered
- UI effect: Component area shows error placeholder or is omitted
- Components affected: All shared components (HeaderNavigation, Heading, TextSection, etc.)

**Image URL Validation:**
- Condition: All image URLs must use HTTPS protocol
- Validation method: Zod schema with URL validation and HTTPS requirement
- Impact: Non-HTTPS URLs are rejected during validation
- UI effect: Component displays without image or shows error state
- Components affected: HeaderNavigation (logo), Heading (background), TextSection (images/icons)

**Color Format Validation:**
- Condition: Theme colors must be valid hex format (#RGB or #RRGGBB)
- Validation method: Regex validation in Zod schema
- Impact: Invalid colors fall back to default theme values
- UI effect: Component uses fallback color instead of invalid value
- Components affected: All components that reference theme colors

**Font Validation:**
- Condition: Font family names must be non-empty strings
- Validation method: Zod string schema with minimum length
- Impact: Invalid fonts fall back to system font stack
- UI effect: Text renders with fallback font
- Components affected: All text-containing components

### Data Integrity Validation

**Layout Structure Validation:**
- Condition: `layout.components` must be an array
- Validation method: Array type check in loader
- Impact: Empty or invalid structure renders empty page with message
- UI effect: Page shows "No content configured" message

**Component Props Completeness:**
- Condition: Each component must have all required props per its Zod schema
- Validation method: Zod parse with error handling
- Impact: Missing required props cause component to skip rendering
- UI effect: Component is omitted from layout
- Components affected: Individually validated per component type

## 10. Error Handling

### Loader Errors

**Network Failures:**
- Scenario: API endpoint is unreachable or times out
- Handling: Loader catches network error and throws 500 Response
- User Experience: ErrorBoundary displays "Failed to load shop data" with server error message
- Recovery: User can refresh page or navigate away

**Shop Not Found (404):**
- Scenario: `shopId` does not exist in database
- Handling: API returns 404, loader throws 404 Response
- User Experience: ErrorBoundary displays "Shop not found" with 404 status
- Recovery: User should check URL or contact support

**Invalid Shop ID (400):**
- Scenario: `shopId` parameter is not a valid UUID
- Handling: Loader validates UUID format before API call, throws 400 Response if invalid
- User Experience: ErrorBoundary displays "Invalid shop ID" message
- Recovery: User needs correct URL with valid shop ID

**Server Error (500):**
- Scenario: Backend API encounters unexpected error
- Handling: API returns 500, loader throws 500 Response
- User Experience: ErrorBoundary displays generic error message
- Recovery: User can try refreshing; issue requires backend investigation

### Component Rendering Errors

**Invalid Component Type:**
- Scenario: Layout includes component type not in registry
- Handling: DynamicComponentRenderer skips unknown component type, logs warning to console
- User Experience: Component is not rendered; page continues with other components
- Recovery: Automatic (graceful degradation)

**Component Validation Failure:**
- Scenario: Component props fail Zod schema validation
- Handling: Validation error is caught, component renders error placeholder or is skipped
- User Experience: Component shows "Configuration error" message or is omitted
- Recovery: Requires fixing configuration in Theme Builder

**Image Load Failure:**
- Scenario: Image URL is valid but image fails to load (404, network error)
- Handling: Browser handles natively, `<img>` element displays alt text or broken image icon
- User Experience: Alt text is shown; layout remains intact
- Recovery: Automatic (browser-level handling)

### Runtime Errors

**Theme Application Error:**
- Scenario: Theme settings have invalid values that bypass validation
- Handling: CSS custom property injection wrapped in try-catch
- User Experience: Page renders with default browser styles
- Recovery: Automatic fallback to default theme

**React Rendering Error:**
- Scenario: Unexpected React error during component render
- Handling: ErrorBoundary catches error and displays error UI
- User Experience: Full page error state with error message
- Recovery: User can refresh page

### Error Logging

**Development Mode:**
- All errors are logged to browser console with full stack traces
- Validation errors show detailed Zod error messages
- API response errors include full response data

**Production Mode:**
- Errors are logged to browser console without sensitive data
- Stack traces are suppressed
- Consider integrating error tracking service (e.g., Sentry) in future iterations

## 11. Implementation Steps

1. **Configure Routes:**
   - Update `app/routes.ts` to add new route configuration for `/shop/:shopId`
   - Import and register the home route component
   - Verify route is accessible via React Router DevTools

2. **Create Loader Function:**
   - Create `loader` function in route file `app/routes/shop.$shopId.tsx`
   - Implement UUID validation for `shopId` parameter
   - Add API call to `/api/public/shops/{shopId}/pages/home`
   - Handle response parsing and error cases
   - Return aggregated loader data with proper TypeScript types

3. **Define TypeScript Types:**
   - Create `app/types/shop.ts` for shared types
   - Define `ShopHomeLoaderData`, `PageLayoutData`, `ComponentConfig`, `ThemeSettings` interfaces
   - Export types for use in route component and renderer

4. **Implement Route Component:**
   - Create route component in `app/routes/shop.$shopId.tsx`
   - Use `useLoaderData<typeof loader>()` to access loader data
   - Implement theme application via CSS custom properties in `useEffect`
   - Pass data to `DynamicComponentRenderer`
   - Add loading state UI for transitions

5. **Build DynamicComponentRenderer:**
   - Create `app/components/DynamicComponentRenderer.tsx`
   - Build component registry mapping type strings to shared components
   - Implement component iteration logic with `.map()`
   - Add Zod validation for each component config
   - Handle validation errors gracefully (skip or show placeholder)
   - Pass validated props to components with `isLoading: false` and `error: null`

6. **Create Component Registry:**
   - Create `app/lib/component-registry.ts`
   - Import all shared components (HeaderNavigation, Heading, TextSection)
   - Export registry object with type-to-component mappings
   - Add TypeScript types for registry structure

7. **Implement Error Boundary:**
   - Add `ErrorBoundary` export in route file
   - Use React Router's `Route.ErrorBoundaryProps` type
   - Handle `isRouteErrorResponse` for HTTP errors
   - Display appropriate error messages for 400, 404, 500
   - Add development-only stack trace display
   - Include recovery action link

8. **Add API Configuration:**
   - Create `app/lib/api.ts` for API utilities
   - Export `API_URL` constant from `import.meta.env.VITE_API_URL`
   - Create helper function for constructing API URLs
   - Add UUID validation utility function

9. **Apply Theme Settings:**
   - In route component, extract theme settings from loader data
   - Use `useEffect` to inject CSS custom properties on `:root`
   - Map theme colors to CSS variables (`--color-primary`, `--color-secondary`, etc.)
   - Map theme fonts to CSS variables (`--font-heading`, `--font-body`)
   - Update global CSS to reference these variables

10. **Test Error Scenarios:**
    - Test with invalid `shopId` (not UUID) to verify 400 error
    - Test with non-existent `shopId` to verify 404 handling
    - Simulate network error to verify 500 error handling
    - Test with malformed API response to verify validation
    - Verify ErrorBoundary displays correct messages for each scenario

11. **Validate Component Rendering:**
    - Verify all shared components render correctly with sample data
    - Test different component variants (sticky header, image backgrounds, etc.)
    - Ensure theme settings are applied to all components
    - Test responsive behavior on mobile, tablet, desktop viewports
    - Validate image loading and fallback behavior
