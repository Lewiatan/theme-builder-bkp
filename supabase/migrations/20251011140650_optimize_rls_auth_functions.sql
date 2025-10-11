-- ============================================================================
-- Migration: Optimize RLS Auth Function Calls
-- Created: 2025-10-11 14:06:50 UTC
-- Description: Optimizes RLS policies by wrapping auth.uid() in SELECT to 
--              prevent re-evaluation for each row, improving query performance
-- ============================================================================
-- Affected Objects:
--   - RLS Policies: All policies on shops and pages tables
-- 
-- Background:
--   When auth.uid() is called directly in RLS policies, PostgreSQL re-evaluates
--   it for every row. Wrapping it as (select auth.uid()) causes PostgreSQL to
--   evaluate it once per query and cache the result, significantly improving
--   performance at scale.
--
-- Reference:
--   https://supabase.com/docs/guides/database/postgres/row-level-security#call-functions-with-select
-- ============================================================================

-- ============================================================================
-- SECTION 1: RECREATE SHOPS TABLE RLS POLICIES
-- ============================================================================

-- drop existing shops policies
drop policy if exists shops_select_policy on shops;
drop policy if exists shops_insert_policy on shops;
drop policy if exists shops_update_policy on shops;
drop policy if exists shops_delete_policy on shops;

-- recreate shops policies with optimized auth function calls

-- policy: users can view only their own shop
create policy shops_select_policy on shops
  for select
  using (user_id = (select auth.uid()));

comment on policy shops_select_policy on shops is 'Users can only view their own shop. Uses (select auth.uid()) for optimal performance.';

-- policy: users can create shops only for themselves
create policy shops_insert_policy on shops
  for insert
  with check (user_id = (select auth.uid()));

comment on policy shops_insert_policy on shops is 'Users can only create shops for themselves. Uses (select auth.uid()) for optimal performance.';

-- policy: users can update only their own shop
create policy shops_update_policy on shops
  for update
  using (user_id = (select auth.uid()))
  with check (user_id = (select auth.uid()));

comment on policy shops_update_policy on shops is 'Users can only update their own shop. Uses (select auth.uid()) for optimal performance.';

-- policy: users can delete only their own shop
-- WARNING: this is a destructive operation that will cascade to pages table
create policy shops_delete_policy on shops
  for delete
  using (user_id = (select auth.uid()));

comment on policy shops_delete_policy on shops is 'Users can only delete their own shop. WARNING: Cascades to pages table. Uses (select auth.uid()) for optimal performance.';

-- ============================================================================
-- SECTION 2: RECREATE PAGES TABLE RLS POLICIES
-- ============================================================================

-- drop existing pages policies
drop policy if exists pages_select_policy on pages;
drop policy if exists pages_insert_policy on pages;
drop policy if exists pages_update_policy on pages;
drop policy if exists pages_delete_policy on pages;

-- recreate pages policies with optimized auth function calls

-- policy: users can view pages belonging to their shop
create policy pages_select_policy on pages
  for select
  using (
    exists (
      select 1 from shops
      where shops.id = pages.shop_id
      and shops.user_id = (select auth.uid())
    )
  );

comment on policy pages_select_policy on pages is 'Users can view pages belonging to their shop. Uses (select auth.uid()) for optimal performance.';

-- policy: users can create pages only for their shop
create policy pages_insert_policy on pages
  for insert
  with check (
    exists (
      select 1 from shops
      where shops.id = pages.shop_id
      and shops.user_id = (select auth.uid())
    )
  );

comment on policy pages_insert_policy on pages is 'Users can create pages only for their shop. Uses (select auth.uid()) for optimal performance.';

-- policy: users can update pages belonging to their shop
create policy pages_update_policy on pages
  for update
  using (
    exists (
      select 1 from shops
      where shops.id = pages.shop_id
      and shops.user_id = (select auth.uid())
    )
  )
  with check (
    exists (
      select 1 from shops
      where shops.id = pages.shop_id
      and shops.user_id = (select auth.uid())
    )
  );

comment on policy pages_update_policy on pages is 'Users can update pages belonging to their shop. Uses (select auth.uid()) for optimal performance.';

-- policy: users can delete pages belonging to their shop
create policy pages_delete_policy on pages
  for delete
  using (
    exists (
      select 1 from shops
      where shops.id = pages.shop_id
      and shops.user_id = (select auth.uid())
    )
  );

comment on policy pages_delete_policy on pages is 'Users can delete pages belonging to their shop. Uses (select auth.uid()) for optimal performance.';

-- ============================================================================
-- MIGRATION NOTES
-- ============================================================================
-- 
-- PERFORMANCE IMPACT:
--   This migration significantly improves RLS policy performance by reducing
--   the number of times auth.uid() is evaluated from O(n) to O(1) per query.
--
-- BEHAVIORAL CHANGES:
--   None - the security logic remains identical. Only the performance 
--   characteristics are improved.
--
-- ROLLBACK:
--   If needed, policies can be recreated with direct auth.uid() calls,
--   though this would reintroduce the performance issue.
--
-- ============================================================================
