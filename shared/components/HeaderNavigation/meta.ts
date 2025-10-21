/**
 * Metadata for the HeaderNavigation component
 * Used by the theme editor for component configuration UI
 */
export const meta = {
  displayName: 'Header/Navigation',
  description: 'Main navigation header with configurable logo and variants',
  category: 'Navigation',
  editableFields: [
    {
      name: 'logoUrl',
      label: 'Logo URL',
      type: 'url',
      required: true,
      description: 'URL to the logo image file',
    },
    {
      name: 'logoPosition',
      label: 'Logo Position',
      type: 'select',
      required: true,
      options: [
        { value: 'left', label: 'Left' },
        { value: 'center', label: 'Center' },
      ],
      default: 'left',
    },
  ],
  variants: [
    {
      value: 'sticky',
      label: 'Sticky',
      description: 'Stays at top when scrolling',
    },
    {
      value: 'static',
      label: 'Static',
      description: 'Fixed position navigation',
    },
    {
      value: 'slide-in-left',
      label: 'Slide-in Left',
      description: 'Side drawer navigation from left',
    },
  ],
  defaultVariant: 'static',
};
