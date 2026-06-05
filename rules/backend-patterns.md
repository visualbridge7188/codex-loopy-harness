# Backend Code Patterns & Guidelines

This document outlines structural rules and conventions for all APIs, database integrations, and middleware.

## 1. REST Endpoints
- Place all API endpoints under `src/api/**` or `src/routes/api/**`.
- Every handler must validate requests before processing data (using Zod or equivalent schemas).
- Handle errors gracefully and return standardized JSON error reports.

## 2. Database Integration (Postgres/Supabase)
- All schema modifications must use migration files (`supabase/migrations/*.sql`).
- Do not write raw, unparameterized SQL queries in code files.
- Activate Row Level Security (RLS) for every table and configure access policies explicitly.

## 3. Security
- Implement Rate Limiting to prevent denial-of-service (DoS) attempts on public endpoints.
- Store sensitive secrets (API tokens, database credentials) as environment variables (referencing `process.env`). Never commit keys.
