## Tech Stack Description

**Frontend - React 19 Single Page Application:**
- React 19 provides a modern, component-based architecture with improved performance and developer experience
- TypeScript ensures static type checking, better IDE support, and reduced runtime errors
- Vite offers lightning-fast development server with Hot Module Replacement (HMR) and optimized production builds
- Tailwind CSS enables rapid, utility-first styling with excellent developer experience
- dnd kit provides modern, accessible drag-and-drop functionality with touch support and excellent performance
- React Router handles client-side routing for seamless page navigation

**Backend & Database - Supabase as comprehensive backend solution:**
- Provides PostgreSQL database with built-in real-time capabilities
- Offers JavaScript/TypeScript SDK that serves as Backend-as-a-Service (BaaS)
- Open source solution that can be self-hosted or run on Supabase cloud
- Includes built-in user authentication with Row Level Security (RLS)
- Eliminates need for custom backend API in MVP phase

**Image Storage - Cloudflare R2:**
- S3-compatible object storage with generous free tier (10GB storage, 1M operations/month)
- Global CDN distribution for fast image delivery
- Cost-effective alternative to traditional cloud storage
- Seamless integration with Cloudflare Workers

**Serverless Functions - Cloudflare Workers:**
- Minimal worker for generating secure presigned upload URLs
- Edge computing with sub-millisecond response times
- Validates user authentication via Supabase JWT
- Keeps R2 credentials secure and never exposed to frontend

**CI/CD & Hosting:**
- GitHub Actions for automated testing, linting, and deployment pipelines
- Cloudflare Pages for frontend hosting with automatic deployments from main branch
- Cloudflare Workers for serverless function deployment via Wrangler
- Automated unit testing (Vitest) and integration testing (Playwright) on pull requests
