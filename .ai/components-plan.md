<conversation_summary>
<decisions>
1.  **Component Sharing:** Components will be shared between the `theme-builder` and `demo-shop` applications via a common directory (`/shared/components`) using TypeScript path mapping and bundler aliases for clean imports.
2.  **Styling:** Each component will be self-contained with its own co-located CSS module file. Global theme settings (colors, fonts) will be applied by the host applications using CSS Custom Properties.
3.  **Data Flow Architecture:** The project will use the **Container/Presentational Pattern**.
    *   **Presentational Components:** Dumb, reusable UI components located in `/shared/components`.
    *   **Container Components:** Smart components located in `/demo-shop/src/containers` that handle data fetching and pass data as props to presentational components.
4.  **Component Configuration:** For static pages, all configurable data will be stored as a single JSON `props` object in the database for each component instance. For the MVP, dynamic data configurations will be simple (e.g., a list of IDs).
5.  **Data Fetching:** Data fetching logic will be encapsulated in entity-specific reusable hooks (e.g., `useProducts`) within the `demo-shop`. These hooks will interact with a centralized API service layer that manages the actual `fetch` calls.
6.  **Component Structure:** A standardized file structure will be used for all shared components: `ComponentName/`, containing `ComponentName.tsx`, `types.ts`, and `ComponentName.module.css`. An `index.ts` will serve as the public export point.
7.  **Component Definition:** Each component's `types.ts` file will define its props via both a TypeScript `interface` for static analysis and a `Zod` schema for runtime validation.
8.  **Editor Integration:** A `meta` object will be defined for each component to inform the `theme-builder`'s editor UI, allowing for the dynamic generation of editing forms.
9.  **State & Performance:** The `theme-builder` will use local React state for editing modals. The `demo-shop` will feature parallel, component-level data fetching with loading states (skeletons) and error states handled by passing `isLoading` and `error` props to presentational components.
10. **Dynamic Pages:** The "Catalog" and "Product" pages are dynamic templates. Their primary data context (e.g., `categoryId`, `productId`) will be derived from the URL at runtime, not from static configuration.
</decisions>
<matched_recommendations>
1.  **TypeScript Path Mapping:** The decision was made to use TypeScript path aliases (`@shared/components`) combined with bundler-specific resolve aliases (Vite for `theme-builder`, React Router for `demo-shop`) to enable clean imports from the shared components directory without the overhead of package management.
2.  **Container/Presentational Pattern:** This recommendation was adopted as the core architectural pattern to separate UI concerns from data-fetching logic, enabling parallel data loading and high reusability.
3.  **Co-located Styles:** The recommendation to co-locate styles with component logic was approved to ensure components are self-contained and render consistently across applications.
4.  **Entity-Specific Data Hooks:** The strategy to create reusable hooks like `useProducts` in the `demo-shop` was confirmed to encapsulate API logic and state management cleanly.
5.  **Component Meta Manifest:** The proposal to have each component export a `meta` object to define its editable properties for the theme editor was accepted.
6.  **Unified Props Object:** The recommendation to treat all configurable data as a single props object was approved to simplify the data model and the editor's functionality.
7.  **Co-located Types and Schemas:** The decision was made to define the TypeScript interface and Zod schema together in each component's `types.ts` file for consistency and maintainability.
8.  **Central API Service Layer:** The recommendation to create a central API service in the `demo-shop` to abstract `fetch` calls was approved to keep data-fetching hooks clean and focused on state management.
</matched_recommendations>
<components_architecture_planning_summary>
### 1. Main Components Architecture Requirements
The architecture is designed around two types of pages: **static pages** (Home, Contact) composed of configurable content blocks, and **dynamic template pages** (Catalog, Product Details) that render data based on the URL. The core principles are **reusability**, **separation of concerns**, and **performance**.

### 2. Component Types
There are two distinct categories of components:

*   **Content Components:** Reusable, configurable blocks that users can place on static pages. Their content (e.g., text, image URLs, lists of product IDs) is stored in the database as part of the page layout.
*   **Page Template Components:** These are special-purpose components that define the layout for dynamic pages. They are not placed by users; instead, they receive their primary data context (like a `productId` or `categoryId`) from the URL via the routing system.

### 3. Common Structure for All Components
Every component in the `/shared/components` library will adhere to the following structure and contract:
*   **File Structure:**
    ```
    /shared/components/ComponentName/
    ├── ComponentName.tsx       # The React component logic (presentational).
    ├── ComponentName.module.css # Co-located styles for the component.
    ├── types.ts                # Exports the TypeScript interface and Zod schema for props.
    ├── meta.ts                 # (For Content Components) Exports the meta object for the theme editor.
    └── index.ts                # Exports the component, types, meta, etc.
    ```
*   **Props Contract:** Every component will accept a props object that includes:
    *   `isLoading: boolean`: To render a skeleton state.
    *   `error: Error | null`: To render an error state.
    *   All other data required for rendering.

### 4. List of Components (MVP)

#### Page Template Components (URL-Driven)
These components are used to build the main dynamic pages of the shop. Their data is derived from URL parameters.

1.  **Product Catalog:**
    *   **Data:** Receives a `categoryId` from the URL. The container component will fetch all products belonging to that category.
    *   **Variants:** Layout (`grid`, `list`), products per page (`12`, `24`).
2.  **Single Product View:**
    *   **Data:** Receives a `productId` from the URL. The container component will fetch the full details for that specific product.
    *   **Variants:** Image gallery position (`left`, `right`).

#### Content Components (Database-Driven)
These are the building blocks users can add to static pages like the Homepage.

3.  **Hero:**
    *   **Data:** `title: string`, `subtitle: string`, `ctaText: string`, `ctaLink: string`, `backgroundImageUrl: string`.
    *   **Variants:** Text alignment (`left`, `center`, `right`).
4.  **Featured Products:**
    *   **Data:** `title: string`, `productIds: string[]`. The container fetches products using these specific IDs.
    *   **Variants:** Number of columns (`3`, `4`).
5.  **Image with Text:**
    *   **Data:** `imageUrl: string`, `title: string`, `text: string`.
    *   **Variants:** Image position (`left`, `right`), text alignment.
6.  **Gallery:**
    *   **Data:** `title: string`, `images: { url: string, alt: string }[]`.
    *   **Variants:** Number of columns (`2`, `3`, `4`).
7.  **Testimonials:**
    *   **Data:** `testimonials: { quote: string, author: string }[]`.
    *   **Variants:** Layout (`slider`, `grid`).
8.  **Contact Form:**
    *   **Data:** `title: string`, `recipientEmail: string`.
    *   **Variants:** Fields displayed (`name`, `email`, `phone`, `message`).
9.  **Map:**
    *   **Data:** `title: string`, `address: string`.
    *   **Variants:** Map style (`roadmap`, `satellite`).
10. **Newsletter Signup:**
    *   **Data:** `title: string`, `placeholderText: string`.
    *   **Variants:** Layout (`inline`, `block`).
11. **Rich Text:**
    *   **Data:** `htmlContent: string`.
    *   **Variants:** None. Styling is derived from global theme settings.
12. **Button:**
    *   **Data:** `text: string`, `link: string`.
    *   **Variants:** Style (`primary`, `secondary`).
13. **Divider:**
    *   **Data:** `height: number`, `color: string`.
    *   **Variants:** Style (`solid`, `dashed`).

### 5. Architectural and Performance Considerations
*   **Routing and Data Loading:** The `demo-shop` will use React Router v7 loaders to fetch the necessary data for dynamic pages (e.g., getting a `productId` from the URL and fetching the product).
*   **Parallel Data Fetching:** For static pages, the container/presentational model allows each Content Component to fetch its data needs in parallel.
*   **Code Splitting:** The page renderer should use `React.lazy()` to dynamically import only the components needed for a specific page, reducing the initial bundle size.
*   **Type Safety:** A dual approach of TypeScript for static analysis and Zod for runtime validation will be used to ensure data integrity from the API to the rendered component.

</components_architecture_planning_summary>
<unresolved_issues>
*   **Component Variants with Different Data:** It is currently assumed that variants are purely stylistic. The question of whether a variant can change the fundamental data schema of a component (e.g., a "Video Hero" variant needing a `videoUrl` instead of `backgroundImageUrl`) is unresolved. This needs to be clarified as components are implemented. If variants require different data, they may need to be treated as distinct components.
</unresolved_issues>
</conversation_summary>
