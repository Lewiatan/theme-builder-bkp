import { lazy } from 'react';
import type { ComponentRegistry, ComponentCategory } from '../types/workspace';

// Import metadata from shared components
import { meta as headingMeta } from '@shared/components/Heading/meta';
import { meta as textSectionMeta } from '@shared/components/TextSection/meta';
import { meta as headerNavigationMeta } from '@shared/components/HeaderNavigation/meta';
import { meta as categoryPillsMeta } from '@shared/components/CategoryPills/meta';
import { meta as productListGridMeta } from '@shared/components/ProductListGrid/meta';

// Lazy load shared components
const Heading = lazy(() => import('@shared/components/Heading'));
const TextSection = lazy(() => import('@shared/components/TextSection'));
const HeaderNavigation = lazy(() => import('@shared/components/HeaderNavigation'));
const CategoryPills = lazy(() => import('@shared/components/CategoryPills'));
const ProductListGrid = lazy(() => import('@shared/components/ProductListGrid'));

/**
 * Component Registry
 *
 * Maps component type identifiers to their metadata and React components.
 * This registry is used throughout the workspace to:
 * - Render components in the library sidebar
 * - Display components on the canvas
 * - Apply default settings when adding new components
 */
// Base registry with kebab-case keys
const baseRegistry: ComponentRegistry = {
  'heading': {
    meta: {
      id: 'heading',
      name: headingMeta.displayName,
      description: headingMeta.description,
      icon: 'Heading',
      category: 'Content' as ComponentCategory,
      variants: headingMeta.variants.map((v) => ({
        id: v.value,
        name: v.label,
        description: v.description,
      })),
      propsSchema: null as any, // Will be set when component validates its own props
    },
    Component: Heading,
    category: 'Content' as ComponentCategory,
    defaultProps: headingMeta.defaultConfig || {
      text: 'Heading Text',
      level: 'h2',
      variant: 'text-only',
    },
  },
  'text-section': {
    meta: {
      id: 'text-section',
      name: textSectionMeta.displayName,
      description: textSectionMeta.description,
      icon: 'FileText',
      category: 'Content' as ComponentCategory,
      variants: textSectionMeta.variants.map((v) => ({
        id: v.value,
        name: v.label,
        description: v.description,
      })),
      propsSchema: null as any,
    },
    Component: TextSection,
    category: 'Content' as ComponentCategory,
    defaultProps: textSectionMeta.defaultConfig || {
      content: 'Text content',
      variant: '1-column',
    },
  },
  'header-navigation': {
    meta: {
      id: 'header-navigation',
      name: headerNavigationMeta.displayName,
      description: headerNavigationMeta.description,
      icon: 'Menu',
      category: 'Navigation' as ComponentCategory,
      variants: headerNavigationMeta.variants.map((v) => ({
        id: v.value,
        name: v.label,
        description: v.description,
      })),
      propsSchema: null as any,
    },
    Component: HeaderNavigation,
    category: 'Navigation' as ComponentCategory,
    defaultProps: headerNavigationMeta.defaultConfig || {
      variant: 'default',
    },
  },
  'category-pills': {
    meta: {
      id: 'category-pills',
      name: categoryPillsMeta.displayName,
      description: categoryPillsMeta.description,
      icon: 'Tags',
      category: 'Navigation' as ComponentCategory,
      variants: categoryPillsMeta.variants.map((v) => ({
        id: v.value,
        name: v.label,
        description: v.description,
      })),
      propsSchema: null as any,
    },
    Component: CategoryPills,
    category: 'Navigation' as ComponentCategory,
    defaultProps: categoryPillsMeta.defaultConfig || {
      variant: 'default',
    },
  },
  'product-list-grid': {
    meta: {
      id: 'product-list-grid',
      name: productListGridMeta.displayName,
      description: productListGridMeta.description,
      icon: 'LayoutGrid',
      category: 'Products' as ComponentCategory,
      variants: productListGridMeta.variants.map((v) => ({
        id: v.value,
        name: v.label,
        description: v.description,
      })),
      propsSchema: null as any,
    },
    Component: ProductListGrid,
    category: 'Products' as ComponentCategory,
    defaultProps: productListGridMeta.defaultConfig || {
      variant: 'grid-3',
    },
  },
};

// Export registry with both kebab-case and PascalCase keys for compatibility
export const componentRegistry: ComponentRegistry = {
  ...baseRegistry,
  // Add PascalCase aliases for backend compatibility
  'Heading': baseRegistry['heading'],
  'TextSection': baseRegistry['text-section'],
  'HeaderNavigation': baseRegistry['header-navigation'],
  'CategoryPills': baseRegistry['category-pills'],
  'ProductListGrid': baseRegistry['product-list-grid'],
};

/**
 * Component categories for organizing the library sidebar
 */
export const componentCategories: ComponentCategory[] = [
  'Content',
  'Navigation',
  'Products',
  'Forms',
  'Media',
];

/**
 * Get component entry by type
 */
export function getComponentByType(type: string) {
  return componentRegistry[type];
}

/**
 * Get all components in a specific category
 * Only returns kebab-case keys to avoid duplicates
 */
export function getComponentsByCategory(category: ComponentCategory) {
  return Object.entries(baseRegistry)
    .filter(([, entry]) => entry.category === category)
    .map(([type, entry]) => ({ type, ...entry }));
}

/**
 * Check if component type exists in registry
 */
export function isValidComponentType(type: string): boolean {
  return type in componentRegistry;
}
