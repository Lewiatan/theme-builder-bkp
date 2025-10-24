// API configuration and utilities

/**
 * Base API URL from environment variable
 */
export const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

/**
 * Constructs a full API URL from a path
 * @param path - API endpoint path (e.g., '/api/public/shops/123')
 * @returns Full API URL
 */
export function buildApiUrl(path: string): string {
  const baseUrl = API_URL.endsWith('/') ? API_URL.slice(0, -1) : API_URL;
  const cleanPath = path.startsWith('/') ? path : `/${path}`;
  return `${baseUrl}${cleanPath}`;
}
