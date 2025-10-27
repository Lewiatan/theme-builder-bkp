import { z } from 'zod';
import { buildApiUrl } from './api';

/**
 * Zod schema for validating a single product
 * Matches the Product interface from shared components
 */
const ProductSchema = z.object({
  id: z.number().int().positive(),
  categoryId: z.number().int().positive(),
  name: z.string().min(1),
  description: z.string(),
  price: z.number().int().nonnegative(),
  salePrice: z.number().int().nonnegative().nullable(),
  imageThumbnail: z.string().min(1),
  imageMedium: z.string().min(1),
  imageLarge: z.string().min(1),
});

/**
 * TypeScript type for a single product
 * Inferred from ProductSchema for type safety
 */
export type Product = z.infer<typeof ProductSchema>;

/**
 * Zod schema for validating products API response
 */
const ProductsResponseSchema = z.object({
  products: z.array(ProductSchema),
});

/**
 * TypeScript type for the full API response
 */
export type ProductsResponse = z.infer<typeof ProductsResponseSchema>;

/**
 * Fetches products filtered by category ID from the backend API
 *
 * This function handles both server-side (SSR loader) and client-side fetching.
 * The API URL is automatically determined based on execution context:
 * - Server-side: Uses Docker service networking (nginx:80)
 * - Client-side: Uses environment variable (localhost:8000)
 *
 * @param categoryId - The category ID to filter products by
 * @returns Promise resolving to array of products in the specified category
 * @throws Error if fetch fails or response validation fails
 *
 * @example
 * ```typescript
 * // In a React Router loader
 * export async function loader({ params }: LoaderFunctionArgs) {
 *   const categoryId = parseInt(params.categoryId, 10);
 *   const products = await fetchProductsByCategory(categoryId);
 *   return { products };
 * }
 * ```
 */
export async function fetchProductsByCategory(categoryId: number): Promise<Product[]> {
  const url = buildApiUrl(`/api/demo/products?categoryId=${categoryId}`);

  try {
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      // Handle 404 specifically for invalid category
      if (response.status === 404) {
        throw new Error(`Category with ID ${categoryId} not found`);
      }

      throw new Error(
        `Failed to fetch products: ${response.status} ${response.statusText}`
      );
    }

    const data: unknown = await response.json();

    // Validate response with Zod
    const validationResult = ProductsResponseSchema.safeParse(data);

    if (!validationResult.success) {
      console.error('Products API response validation failed:', validationResult.error);
      throw new Error('Invalid products data received from server');
    }

    return validationResult.data.products;
  } catch (error) {
    // Re-throw with context
    if (error instanceof Error) {
      console.error(`Error fetching products for category ${categoryId}:`, error.message);
      throw error;
    }

    // Handle unexpected error types
    console.error('Unexpected error fetching products:', error);
    throw new Error('An unexpected error occurred while fetching products');
  }
}

/**
 * Fetches all products without category filtering from the backend API
 *
 * This function handles both server-side (SSR loader) and client-side fetching.
 * The API URL is automatically determined based on execution context:
 * - Server-side: Uses Docker service networking (nginx:80)
 * - Client-side: Uses environment variable (localhost:8000)
 *
 * @returns Promise resolving to array of all products
 * @throws Error if fetch fails or response validation fails
 *
 * @example
 * ```typescript
 * // In a React Router loader
 * export async function loader() {
 *   const products = await fetchAllProducts();
 *   return { products };
 * }
 *
 * // In a client-side hook
 * useEffect(() => {
 *   fetchAllProducts()
 *     .then(setProducts)
 *     .catch(setError);
 * }, []);
 * ```
 */
export async function fetchAllProducts(): Promise<Product[]> {
  const url = buildApiUrl('/api/demo/products');

  try {
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error(
        `Failed to fetch products: ${response.status} ${response.statusText}`
      );
    }

    const data: unknown = await response.json();

    // Validate response with Zod
    const validationResult = ProductsResponseSchema.safeParse(data);

    if (!validationResult.success) {
      console.error('Products API response validation failed:', validationResult.error);
      throw new Error('Invalid products data received from server');
    }

    return validationResult.data.products;
  } catch (error) {
    // Re-throw with context
    if (error instanceof Error) {
      console.error('Error fetching all products:', error.message);
      throw error;
    }

    // Handle unexpected error types
    console.error('Unexpected error fetching products:', error);
    throw new Error('An unexpected error occurred while fetching products');
  }
}
