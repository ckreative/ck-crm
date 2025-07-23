# CLAUDE.md

## Project Stack

- Laravel PHP
- Tailwind CSS
- Supabase
- Alpine.js (for frontend interactivity)

---

## Purpose

This document sets strict guidelines for working in this codebase to protect architecture integrity and design consistency.

No UI or UX design changes are allowed unless explicitly requested and approved.

---

## General Principles

- Follow all existing design, architecture, and style guidelines.
- Prioritize maintainability, clarity, and security.
- Avoid introducing new patterns without documented approval.

---

## UI & UX Rules

Allowed:
- Bug fixes that do not alter visual design.
- Content changes that do not affect layout.

Not allowed:
- Modifying layout, styling, colors, typography, or interactions without approval.
- Creating new components or design patterns without design team sign-off.

---

## UX Design Detailed Rules

1. Do not change the UI design unless explicitly requested.
2. Respect established design systems.
   - Use pre-approved tokens, components, and patterns.
3. Consistency is critical.
   - Maintain structure, spacing, and interaction patterns throughout.
4. User flows must be predictable and logical.
   - Avoid introducing unexpected steps or changes.
5. Prioritize clarity over creativity.
   - Ensure UI is immediately understandable.
6. Respect accessibility standards.
   - Maintain proper contrast and assistive technology support.
7. Content hierarchy and information architecture should not be altered.
   - No reorganizing screens or menus without approval.
8. Feedback and error states must remain clear and actionable.
   - Keep messaging consistent.
9. Do not remove or hide features without approval.
   - All functionality changes require full design and product review.
10. Document rationale for any allowed changes.
    - Include before/after visuals and a short written explanation.

---

## UX & UI Laws

- Hick's Law: Minimize choices to reduce user decision time.
- Fitts's Law: Design interactive elements large enough and close enough to be easily clicked or tapped.
- Jakob's Law: Users prefer your product to work the same way as other products they already know.
- Law of Proximity: Elements placed close together are perceived as related.
- Law of Similarity: Items that look similar are perceived as part of the same group.
- Miller's Law: Users can only hold about 7 (plus or minus 2) items in their working memory.
- Occam's Razor: Choose the simplest solution that works.
- Pareto Principle: Focus on the 20% of features that deliver 80% of value.
- Peak-End Rule: Users judge an experience largely based on how they felt at its peak and at its end.
- Serial Position Effect: Users remember the first and last items best.
- Tesler's Law: Every system has a certain amount of complexity that cannot be reduced — move complexity away from the user when possible.

---

## Database (Postgres & Supabase)

### Migrations

- Use versioned migration files; each migration must be clear and incremental.
- Include down scripts to ensure reversibility.
- Document all schema changes clearly.

### Functions

- Write small, single-purpose Postgres functions.
- Use consistent naming: {action}_{entity}[_qualifier] (e.g., get_user_by_email, create_order, update_profile_name, delete_session).
- Prefer SQL functions over PL/pgSQL for simple queries to improve performance.

### Postgres Function Usage & Naming Policy

- **All database queries must be implemented as Postgres functions.**
- **There must never be more than one function for the same task.**
  - Each function must have a unique, clearly defined responsibility.
  - Do not duplicate logic across multiple functions; always reuse the existing function for a given task.
- **Naming Convention:**  
  - The action verb must always come first, followed by the entity and any qualifiers.
  - Format: `{action}_{entity}[_qualifier]`  
    - Examples: `get_user_by_email`, `create_order`, `update_profile_name`, `delete_session`
- Direct SQL queries from the application layer are not allowed unless there is a documented and approved exception.
- Any deviation from this rule must be reviewed and approved by the database architect or lead developer.

### RLS (Row-Level Security)

- Enable RLS by default on all tables.
- Policies must follow least privilege principle.
- Separate read and write policies explicitly.
- **Follow Supabase RLS Guidelines:**

#### RLS Policy Rules:
- Use only CREATE POLICY or ALTER POLICY queries.
- Always use "auth.uid()" instead of "current_user".
- SELECT policies: use USING but not WITH CHECK.
- INSERT policies: use WITH CHECK but not USING.
- UPDATE policies: use both WITH CHECK and USING.
- DELETE policies: use USING but not WITH CHECK.
- Don't use `FOR ALL` - create separate policies for select, insert, update, and delete.
- Always specify roles with `TO authenticated` or `TO anon`.
- Use descriptive policy names in double quotes.

#### Performance Guidelines:
- Add indexes on columns used in policies.
- Wrap functions with `select` (e.g., `(select auth.uid())`).
- Minimize joins - use `IN` operations instead.
- Always specify roles to prevent unnecessary policy execution.

#### Example Policy Structure:
```sql
CREATE POLICY "Users can view their own profiles" 
ON profiles 
FOR SELECT 
TO authenticated 
USING ((select auth.uid()) = id);

CREATE POLICY "Users can update their own profiles"
ON profiles
FOR UPDATE
TO authenticated
USING ((select auth.uid()) = id)
WITH CHECK ((select auth.uid()) = id);
```

### SQL Style Guide

- Always use lowercase for table and column names.
- Use snake_case consistently.
- Indent nested SELECTs clearly for readability.
- Comment complex joins or subqueries to improve clarity.

---

## API & Edge Functions

- Create edge functions with single, well-defined purposes.
- Validate and sanitize all input strictly.
- Return standardized, clear JSON responses.
- Separate business logic from request parsing and response formatting.
- Always handle and log errors explicitly.

---

## Laravel PHP Guidelines

### Code Style and Structure

- Follow PSR-12 coding standards.
- Use PHP 8+ features appropriately (named arguments, attributes, etc.).
- Prefer type declarations and return types for all methods.
- Use dependency injection over facades where appropriate.
- Keep controllers thin - move business logic to services or actions.
- Use Laravel's built-in validation features.

### Naming Conventions

- Use PascalCase for class names.
- Use camelCase for method and variable names.
- Use snake_case for database columns and table names.
- Use kebab-case for routes and URLs.
- Use descriptive names that clearly indicate purpose.

### Laravel Best Practices

- Use Eloquent ORM properly - avoid N+1 queries.
- Implement repository pattern for complex data access logic.
- Use Laravel's authorization features (policies and gates).
- Leverage Laravel's caching mechanisms appropriately.
- Use queues for time-consuming tasks.
- Implement proper logging and error handling.
- Use database transactions for data integrity.
- Follow RESTful conventions for API routes.

### Security

- Always validate and sanitize user input.
- Use Laravel's CSRF protection.
- Implement proper authentication and authorization.
- Never expose sensitive information in responses.
- Use environment variables for configuration.
- Keep dependencies updated.
- Use prepared statements (Eloquent/Query Builder handles this).

### Performance

- Use eager loading to prevent N+1 queries.
- Implement proper database indexing.
- Use caching strategically (Redis/Memcached).
- Optimize database queries.
- Use pagination for large datasets.
- Implement API rate limiting.

---

## Styling Guidelines

- Use existing Tailwind utility classes; avoid custom styles unless approved.
- Keep blade templates clean and organized.
- Extract reusable UI components into blade components.
- Maintain mobile-first responsive design approach.
- Never use the @apply directive in Tailwind.

---

## Style Guide & Implementation

- Follow the style guide rigorously.
- Do not introduce new color tokens, typography, or spacing without approval.
- Use design tokens and documented variants for consistency.

---

## Requirements Gathering & UX

- Always start with clear, written requirements before implementation.
- Engage the design/UX team early if there's any ambiguity.
- User flows and edge cases must be documented.
- Never assume or alter UX on your own — escalate for clarification.

---

## Testing Guidelines

### Playwright Testing

- Store all E2E tests in `tests/e2e/` directory, organized by feature.
- Never commit test artifacts to version control.
- After running Playwright tests, always clean up generated artifacts.

### Playwright Artifacts to Clean Up

The following files and directories must be cleaned up after Playwright test runs:

- Screenshots: `*.png` files in project root
- Test results: `/test-results/` directory
- Reports: `/playwright-report/` directory
- Cache: `/playwright/.cache/` and `/.playwright/` directories
- HTML outputs: `*-output.html` files in project root
- Trace files: `*.trace.zip` files
- Video recordings: `*.webm` files

### Automated Cleanup

- Always run `npm run test:e2e:clean` after Playwright tests.
- Check for and remove any stray artifacts before committing.
- Use designated directories (`tests/e2e/artifacts/`) for intentional test outputs.

### Test Organization

- Group tests by feature: `tests/e2e/auth/`, `tests/e2e/dashboard/`, etc.
- Use descriptive test names that explain the scenario being tested.
- Keep test files focused on a single feature or user flow.

---

## Final Reminders

- Wait for explicit approval for design, UX, or architecture changes.
- Focus on stable, predictable, and minimal-impact updates.

---

No UI or UX design changes unless explicitly requested.

Thank you for keeping this project disciplined and future-proof.