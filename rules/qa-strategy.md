# QA Strategy & Verification Guidelines

This document establishes quality criteria, E2E testing scenarios, and severity rules.

## 1. Automated Testing Levels (L0–L5)
- **L0 (Unit):** Basic utility function tests. Fast, offline execution.
- **L1 (Integration):** API endpoint tests, mock database states.
- **L2 (E2E Functional):** Browser E2E interaction scripts (Playwright).
- **L3 (Visual QA):** Layout audits and contrast ratio verifications.
- **L4 (Security Audit):** OWASP dependency checks and vulnerability scans.
- **L5 (Performance Audit):** Core Web Vitals profiling (Largest Contentful Paint, etc.).

## 2. Severity Classification Rules
- **CRITICAL:** Total blocker (e.g. build failure, security leak, core action crash). **Must fix.**
- **HIGH:** Functional blocker (e.g. broken nav link, missing database constraint). **Must fix.**
- **MEDIUM:** Non-blocking functional bug (e.g. minor animation glitch, layout warp on tablet). Fix in current sprint or log.
- **LOW:** Suggestion or design polish item. Track in backlog.

## 3. QA Feedback Loop Execution
- Prior to pushing code, the Specialist runs the pre-push QA suite.
- If E2E tests report errors, route findings to the `bug-fixer` agent for automatic patch attempts (up to 5 rounds).
