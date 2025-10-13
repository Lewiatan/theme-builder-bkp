# E-commerce Theme Builder - Project Specification (MVP)

## Project Overview

A **minimum viable product (MVP)** for an E-commerce Theme Builder that enables store owners to customize shop page layouts and appearance through a visual drag-and-drop interface. This application serves as both a **10xDevs certification project** (demonstrating proficiency in AI-Assisted Development techniques) and the foundational element for a future **E-commerce SaaS** platform.

**Target Timeline:** First certification deadline in 1 month, second in 2 months

## Business Requirements

### Core Features

#### Component-Based Design System
- Templates built from a library of **predefined components** (e.g., Header, Banner, Product List)
- Each component supports multiple **predefined variants**
  - **Example:** Text component with 1/2/3/4 column layouts
  - **Example:** Header component with various logo placements and background sizes
- Users can **only select from available variants** (no custom variant creation in MVP)
- Users **must be able to edit content** within chosen variants:
  - Text content
  - Images (upload/selection)
  - Links
  - Product selection (e.g., for featured products component)
- Users **cannot modify** individual component colors or fonts
- **Global theme settings** available for entire template:
  - Color palette (applies to all components)
  - Typography/fonts (applies to all components)
  - Accessed via collapsible Theme Settings sidebar (see Theme Settings Sidebar section)
  - Saved independently from page content changes

#### Drag-and-Drop Interface
- Visual arrangement of components using **Drag and Drop**
- Vertical component stacking (one below another)
- Column layouts handled through component variants
- **UI Layout:**
  - **Top bar:** Navigation and action controls (see Workspace Top Bar section)
  - **Left sidebar:** Vertical, scrollable component library/palette
  - **Center:** Main workspace/editor area (canvas) for arrangement
  - **Right sidebar:** Collapsible theme settings panel (see Theme Settings Sidebar section)
- **Editing Controls:** Component controls (move up/down, settings, delete) appear only on **hover** in workspace
- Components render clean in workspace, imitating final output

#### Workspace Top Bar
- **Left Section:** Page dropdown menu
  - Displays all available page types (Home, Product Catalog, Product Detail, Contact)
  - Allows switching between pages during editing
  - Shows currently active page
- **Center Section:** Status indicator
  - Displays "Unsaved changes" text when current page has modifications
  - Hidden when no changes present
- **Right Section:** Action buttons (left to right)
  - **Reset button:** Discards all unsaved changes on current page, restores last saved state
  - **Save button:** Saves current page changes to database
  - **Demo button:** Opens Demo Store home page in new tab
  - **Theme Settings button:** Cog icon button that toggles Theme Settings sidebar visibility
  - Save and Reset buttons are **disabled by default**, enabled only when current page has unsaved changes

**Unsaved Changes Protection:**
- Confirmation dialog appears when user attempts to navigate away with unsaved changes (page content, theme settings, or both):
  - Switching to different page via dropdown
  - Navigating to other routes (logout, settings, etc.)
  - Closing browser tab/window
  - Opening Demo Store (Demo button)
- Dialog message clearly specifies what is unsaved:
  - "You have unsaved page changes"
  - "You have unsaved theme settings"  
  - "You have unsaved page changes and theme settings"
- Dialog options: "Stay on page" or "Leave without saving"
- Choosing "Leave without saving" discards all unsaved changes (page and/or theme)
- **Reset button confirmation:** Shows confirmation dialog before discarding changes to prevent accidental data loss

#### Theme Settings Sidebar
- **Location:** Right side of workspace, collapsible panel
- **Default State:** Collapsed (completely hidden)
- **Toggle:** Via cog icon button in top bar right section
- **Persistence:** Remains open/closed when switching between pages (does not auto-close)
- **Content:** 
  - Color palette picker (applies globally to all components)
  - Font/typography selection (applies globally to all components)
  - "Save Theme Settings" button at bottom
- **Save Button State:**
  - Disabled by default (when no changes made)
  - Enabled when theme settings modified
- **Live Preview:** Changes to theme settings visible immediately in workspace canvas
- **Save Workflow:** Independent from page-level saves
  - Theme settings saved via "Save Theme Settings" button in sidebar
  - Page content saved via "Save" button in top bar
  - Two separate save operations

#### Page Management
Support for building templates for four core E-commerce page types:
1. **Home Page**
2. **Product Catalog**
3. **Product Detail Page**
4. **Contact Page**

#### Live Preview & Demo Store

**Live Preview (Workspace)**
- Real-time preview of changes in the workspace canvas
- Shows **unsaved changes** immediately for both:
  - Page content modifications (components, variants, content)
  - Theme settings changes (colors, fonts)
- Updates as user modifies components or theme settings
- **Optional enhancement:** If live preview proves difficult for MVP scope, add "Preview" button that renders current snapshot of settings for the active page only

**Demo Store (Separate View)**
- Dedicated view/route for previewing published templates
- Renders page templates using **SAVED settings only** from database
- Displays **mocked product data** (static JSON array in application code)
- Changes visible in Live Preview are **NOT shown** in Demo Store until saved

#### Save & Reset Functionality
- User must **explicitly confirm** saving changes via appropriate save button:
  - **Page changes:** "Save" button in top bar
  - **Theme settings:** "Save Theme Settings" button in Theme Settings sidebar
- **"Reset" button** in top bar to discard all unsaved page changes and restore last saved state from database
  - Requires confirmation dialog to prevent accidental data loss
- Theme settings have no reset button (user can simply close sidebar without saving)
- No auto-save in MVP
- No undo/redo in MVP
- **Page navigation behavior:** Switching pages with unsaved changes triggers confirmation dialog; choosing to leave discards all unsaved changes on that page

#### User Management & Authentication
- Secure user authentication required
- Each user can manage their shop templates
- User/session isolation

### Optional Features (Priority Order)

1. **Viewport Switching in Workspace:** Toggle between mobile/tablet/desktop screen sizes
2. **Viewport Switching in Live Preview:** Toggle between mobile/tablet/desktop views
3. **Multi-tenant Support:** Multiple users/shops with separate configurations
4. **Per-shop Demo Stores:** Separate demo for each shop (shared product mock catalog acceptable)

## Technical Requirements

### Technology Stack

#### Frontend
- **Framework:** React 19 with TypeScript (Single Page Application - SPA)
- **Build Tool:** Vite
- **Styling:** Tailwind CSS
- **Drag & Drop:** dnd kit
- **Routing:** React Router
- **HTTP Client:** Fetch API for REST API communication
- **Package Manager:** npm

#### Backend API
- **Framework:** Symfony 7.3
- **Language:** PHP 8.2+
- **API Style:** REST API with JSON responses
- **Authentication:** JWT tokens (using LexikJWTAuthenticationBundle)
- **Validation:** Symfony Validator component
- **Serialization:** Symfony Serializer component
- **API Documentation:** Nelmio API Doc Bundle (optional for MVP, auto-generates OpenAPI/Swagger docs)

#### Database & Services
- **Database:** PostgreSQL (standalone, self-hosted or cloud-hosted)
- **ORM:** Doctrine ORM
- **Migrations:** Phinx
- **Image Storage:** Cloudflare R2 (S3-compatible object storage)
- **Image Upload:** Direct upload via Symfony API endpoints using AWS SDK for PHP (S3-compatible)

#### Architecture
- **Three-Tier Architecture:** Frontend (React SPA) → Backend API (Symfony REST API) → Database (PostgreSQL)
- **API-First Approach:** All data operations flow through REST API endpoints
- **Stateless Backend:** JWT-based authentication, no server-side sessions
- **Monorepo Approach:** Preferred for MVP to ensure rapid development and code sharing
- **Project Structure:**
  ```
  theme-builder/
  ├── frontend/                    # React SPA
  │   ├── src/
  │   │   ├── components/         # React components
  │   │   ├── pages/              # Page components
  │   │   ├── api/                # API client & HTTP utilities
  │   │   ├── types/              # TypeScript types
  │   │   └── hooks/              # Custom React hooks
  │   ├── public/
  │   └── package.json
  ├── backend/                     # Symfony API
  │   ├── src/
  │   │   ├── Controller/         # API endpoints
  │   │   ├── Entity/             # Doctrine entities
  │   │   ├── Repository/         # Data repositories
  │   │   ├── Service/            # Business logic services
  │   │   ├── Security/           # Authentication & authorization
  │   │   └── EventListener/      # Event subscribers
  │   ├── config/                 # Symfony configuration
  │   ├── db/
  │   │   └── migrations/         # Phinx migrations
  │   └── composer.json
  └── shared/                      # Shared type definitions (optional)
      └── types/                   # TypeScript/PHP type definitions
  ```
- **Clear Separation:** Frontend and backend can be developed, tested, and deployed independently

### Image Storage & Upload
- **Storage:** Cloudflare R2 (S3-compatible object storage)
- **Upload Flow:**
  1. Frontend sends POST request to `/api/shops/{shopId}/images` with image file and JWT in Authorization header
  2. Symfony validates JWT and verifies user has access to the specified shop
  3. If authorized, Symfony uploads image to R2 using AWS SDK for PHP (configured for R2 endpoint)
  4. Symfony stores public image URL in database and returns it to frontend
  5. Frontend uses the returned URL in template JSON configuration
- **Security:**
  - R2 credentials never exposed to frontend (stored in Symfony environment variables)
  - Only authenticated users with shop access can upload images (JWT + shop ownership validation)
  - File type and size validation performed by Symfony before upload
- **Cost:** Free tier covers MVP needs (10GB storage, 1M operations/month)
- **Implementation:** AWS SDK for PHP used for S3-compatible API communication with R2 (R2 implements S3 protocol)

### Authentication & Security
- **Authentication:** JWT-based authentication using LexikJWTAuthenticationBundle
- **Login Flow:**
  1. User submits credentials to `/api/auth/login`
  2. Symfony validates credentials against database
  3. If valid, Symfony generates JWT token and returns it to frontend
  4. Frontend stores JWT (e.g., in localStorage or httpOnly cookie)
  5. Frontend includes JWT in Authorization header for subsequent requests
- **Authorization:** Symfony Security component with custom voters for shop-level access control
- **Password Security:** Passwords hashed using Symfony's PasswordHasher (bcrypt/argon2)
- **Data Access Control:** Repository-level filtering ensures users can only access their own shops/themes
- **Image Upload Security:** Shop ownership validation before allowing image uploads

### Data Persistence
- Template settings stored in database as **clean JSON structure** in a JSON column
- JSON format chosen for easy data structure and quick MVP development
- No versioning in MVP (potential future feature)

### Database Schema (Simplified)

```sql
-- users
- id (PRIMARY KEY)
- email (UNIQUE)
- password (hashed)
- created_at
- updated_at

-- shops
- id (PRIMARY KEY)
- user_id (FOREIGN KEY -> users.id)
- name
- created_at
- updated_at

-- themes
- id (PRIMARY KEY)
- shop_id (FOREIGN KEY -> shops.id)
- global_settings (JSONB: colors, fonts)
- created_at
- updated_at

-- pages
- id (PRIMARY KEY)
- theme_id (FOREIGN KEY -> themes.id)
- type (ENUM: home, catalog, product, contact)
- components (JSONB: array of components with variants and content)
- published_at
- created_at
- updated_at
```

### Data Source
- Mocked product catalog maintained as **static JSON array** within application code
- No real product database integration in MVP

### Deployment & CI/CD
- **Hosting:**
  - Frontend: Cloudflare Pages
  - Backend API: Render Web Service (PHP/Symfony)
  - Database: Render PostgreSQL
  - Image Storage: Cloudflare R2
- **CI/CD:** GitHub Actions for:
  - Automated testing (unit + integration) on Pull Request
  - Linting (frontend: ESLint, backend: PHP CS Fixer) on Pull Request
  - Automatic deployment of frontend to Cloudflare Pages on merge to `main`
  - Automatic deployment of backend API to Render on merge to `main` (via Render's GitHub integration)

### Testing
- **Frontend Unit Tests:** Vitest
- **Backend Unit Tests:** PHPUnit
- **E2E/Integration Tests:** Playwright
- 100% coverage NOT required
- Tests must run automatically via GitHub Actions

### Rendering
- No Server-Side Rendering (SSR) required
- Client-side rendering only (no SEO requirements)

## Component Library (13 Components)

The following components cover all required page types (Home, Catalog, Product, Contact):

### 1. Heading
- **Variants:** Text only, Text with background image
- **Editable:** Heading text, heading level (H1/H2/H3), background image (for variant with image)

### 2. Header/Navigation
- **Variants:** Sticky/static, with mega menu, simple horizontal, with search bar
- **Editable:** Logo, menu links, logo position (left/center)

### 3. Footer
- **Variants:** 2/3/4 columns, simple (1 column)
- **Editable:** Sections (About, Contact, Social media), links, copyright text

### 4. Hero/Full-Width Banner
- **Variants:** Full-width with background image, with video, split layout (image + text)
- **Editable:** Title, subtitle, CTA button, background image/video

### 5. Text Section
- **Variants:** 1/2/3/4 columns, with icons, with images
- **Editable:** Text content, optional images

### 6. Call-to-Action (CTA) Block
- **Variants:** Full-width, box with shadow, with background image
- **Editable:** Header, text, button, background image

### 7. Product List/Grid
- **Variants:** 2/3/4/6 products per row, with/without filters
- **Editable:** Product selection from mock catalog, number of rows
- **Use:** Catalog page, Home page sections

### 8. Featured Products
- **Variants:** Carousel, grid, list
- **Editable:** Manual product selection from mock catalog (max 8-12 products)
- **Use:** Home page, promotional sections

### 9. Product Detail View
- **Variants:** Large image gallery, compact view, with quick specifications
- **Editable:** Product selection, visible fields (price, description, specs, reviews)
- **Use:** Product detail page

### 10. Review/Testimonial Section
- **Variants:** Carousel, 2/3 column grid, list
- **Editable:** Add reviews (name, content, rating, avatar)

### 11. Contact Form
- **Variants:** Simple (name, email, message), extended (+ phone, subject)
- **Editable:** Section title, info text, visible fields

### 12. Image Gallery
- **Variants:** Masonry, equal grid, carousel, lightbox
- **Editable:** Upload images, alt descriptions

### 13. Map/Location Block
- **Variants:** Full-width map, split (map + info), compact embed
- **Editable:** Address/coordinates, info text, map zoom level

## Future Considerations

- Advanced image processing:
  - Image optimization and compression before upload
  - Automatic thumbnail generation
  - Format conversion (WebP, AVIF)
  - Cloudflare Images integration for automatic optimization and resizing
- MCP (Model Context Protocol) integration for LLM-based template manipulation
- Template versioning and history
- Component limitations per page type
- Advanced undo/redo functionality
- Multi-language support
- Real product catalog integration
- Advanced image optimization

## Success Criteria

- ✅ Functional drag-and-drop interface with hover controls
- ✅ All 13 components working with variants and content editing
- ✅ Save/reset functionality
- ✅ Demo store with mocked products (static JSON)
- ✅ Live preview showing unsaved changes
- ✅ Authentication working with RLS
- ✅ Global theme settings (colors, fonts)
- ✅ Automated tests passing
- ✅ CI/CD pipeline operational
- ✅ Deployed and accessible application on Cloudflare Pages

## Project Context

- Developer has 14 years of experience (PHP, TypeScript, Angular backend focus)
- Limited React experience (recently learned)
- Project serves dual purpose: **10xDevs certification** + real product foundation
- Emphasis on **AI-Assisted Development** techniques throughout the project
- Clean architecture prioritized for future scalability and easy backend decoupling