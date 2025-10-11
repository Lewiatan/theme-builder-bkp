-- ============================================================================
-- Migration: Fix Function Search Path Security
-- Created: 2025-10-11 14:14:45 UTC
-- Description: Sets explicit search_path on update_updated_at_column function
--              to prevent potential security vulnerabilities from search path
--              manipulation attacks
-- ============================================================================
-- Affected Objects:
--   - Function: update_updated_at_column()
-- 
-- Background:
--   Functions without an explicit search_path setting inherit the caller's
--   search_path, which can lead to security vulnerabilities. A malicious user
--   could manipulate the search_path to inject unexpected behavior.
--
--   Best practice: Set search_path to an empty string or specific schemas
--   to ensure the function always resolves objects predictably.
--
-- Reference:
--   https://www.postgresql.org/docs/current/sql-createfunction.html
--   https://supabase.com/docs/guides/database/postgres/schema#search-path
-- ============================================================================

-- recreate the function with explicit search_path security setting
create or replace function update_updated_at_column()
returns trigger
language plpgsql
security definer
set search_path = ''
as $$
begin
  new.updated_at = now();
  return new;
end;
$$;

comment on function update_updated_at_column is 'Automatically updates updated_at column to current timestamp on UPDATE operations. Has explicit search_path for security.';

-- ============================================================================
-- MIGRATION NOTES
-- ============================================================================
-- 
-- SECURITY IMPROVEMENTS:
--   - set search_path = '' ensures the function doesn't inherit caller's path
--   - security definer with empty search_path prevents search path attacks
--   - Function behavior remains identical, only security posture is improved
--
-- WHY EMPTY STRING:
--   An empty search_path forces explicit schema qualification (e.g., pg_catalog.now())
--   or uses only system catalogs. Since our function only uses built-in functions
--   (now()), this is the safest approach.
--
-- BEHAVIORAL CHANGES:
--   None - The function logic is identical, only security attributes changed.
--
-- TRIGGERS:
--   No need to recreate triggers - they reference the function by name and will
--   automatically use the updated version.
--
-- ============================================================================
