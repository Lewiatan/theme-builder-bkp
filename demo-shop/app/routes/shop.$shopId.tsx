import { useEffect, useState } from "react";
import { useParams } from "react-router";
import { buildApiUrl } from "~/lib/api";
import { isValidUuid } from "~/lib/validation";
import type { ShopHomeLoaderData, PageLayoutData } from "~/types/shop";
import { Alert, AlertDescription, AlertTitle } from "~/components/ui/alert";
import { Button } from "~/components/ui/button";
import { Skeleton } from "~/components/ui/skeleton";
import DynamicComponentRenderer from "~/components/DynamicComponentRenderer";

/**
 * Shop Home Route Component
 * Displays the shop's home page with dynamic components
 * Uses client-side data fetching (SPA mode)
 */
export default function ShopHomeRoute() {
  const { shopId } = useParams();
  const [data, setData] = useState<ShopHomeLoaderData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<{ status: number; message: string } | null>(null);

  // Fetch shop data on mount or when shopId changes
  useEffect(() => {
    async function fetchShopData() {
      // Validate shopId format
      if (!shopId || !isValidUuid(shopId)) {
        setError({ status: 400, message: "Invalid shop ID format" });
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        setError(null);

        // Fetch page data from API
        const response = await fetch(
          buildApiUrl(`/api/public/shops/${shopId}/pages/home`)
        );

        if (!response.ok) {
          if (response.status === 404) {
            setError({ status: 404, message: "Shop not found" });
          } else {
            setError({ status: 500, message: "Failed to load shop data" });
          }
          setLoading(false);
          return;
        }

        const pageData: PageLayoutData = await response.json();

        // Set aggregated data
        setData({
          shopId,
          page: pageData,
          theme: {}, // TODO: Fetch theme settings in future iteration
        });
        setLoading(false);
      } catch (err) {
        console.error("Error loading shop data:", err);
        setError({ status: 500, message: "Failed to load shop data" });
        setLoading(false);
      }
    }

    fetchShopData();
  }, [shopId]);

  // Apply theme settings via CSS custom properties
  useEffect(() => {
    if (!data?.theme) return;

    const root = document.documentElement;

    if (data.theme.colors) {
      if (data.theme.colors.primary) {
        root.style.setProperty('--color-primary', data.theme.colors.primary);
      }
      if (data.theme.colors.secondary) {
        root.style.setProperty('--color-secondary', data.theme.colors.secondary);
      }
      if (data.theme.colors.background) {
        root.style.setProperty('--color-background', data.theme.colors.background);
      }
      if (data.theme.colors.text) {
        root.style.setProperty('--color-text', data.theme.colors.text);
      }
    }

    if (data.theme.fonts) {
      if (data.theme.fonts.heading) {
        root.style.setProperty('--font-heading', data.theme.fonts.heading);
      }
      if (data.theme.fonts.body) {
        root.style.setProperty('--font-body', data.theme.fonts.body);
      }
    }
  }, [data?.theme]);

  // Loading state
  if (loading) {
    return (
      <div className="min-h-screen p-8">
        <div className="space-y-4">
          <Skeleton className="h-20 w-full" />
          <Skeleton className="h-64 w-full" />
          <Skeleton className="h-32 w-full" />
        </div>
      </div>
    );
  }

  // Error state
  if (error) {
    let title = "Error";
    let description = "An unexpected error occurred";

    switch (error.status) {
      case 400:
        title = "Invalid Shop ID";
        description = "The shop ID provided is not valid. Please check the URL and try again.";
        break;
      case 404:
        title = "Shop Not Found";
        description = "The shop you're looking for doesn't exist or has been removed.";
        break;
      case 500:
        title = "Server Error";
        description = "We're having trouble loading this shop. Please try again later.";
        break;
    }

    return (
      <div className="min-h-screen flex items-center justify-center p-4">
        <div className="max-w-md w-full">
          <Alert variant="destructive">
            <AlertTitle className="text-lg font-semibold mb-2">
              {title}
            </AlertTitle>
            <AlertDescription className="mb-4">
              {description}
            </AlertDescription>
          </Alert>
          <div className="mt-4 flex gap-2">
            <Button
              variant="outline"
              onClick={() => window.history.back()}
              className="flex-1"
            >
              Go Back
            </Button>
            <Button
              onClick={() => window.location.reload()}
              className="flex-1"
            >
              Try Again
            </Button>
          </div>
        </div>
      </div>
    );
  }

  // Success state - render components
  if (!data) return null;

  return (
    <div className="min-h-screen">
      <DynamicComponentRenderer layout={data.page} themeSettings={data.theme} />
    </div>
  );
}

