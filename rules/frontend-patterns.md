# Frontend Code Patterns & Guidelines

This document outlines structural rules and conventions for all frontend components and page files.

## 1. Structure and Organization
- Place all UI components in `src/components/` (keep them small, focused, and reusable).
- Place all routing pages in `src/pages/`.
- Manage client state using custom hooks in `src/hooks/`.

## 2. Design System and Styling
- Maintain visual harmony: use curated, rich HSL palettes (no plain default colors).
- Styling must use Vanilla CSS stylesheets or TailwindCSS classes.
- Ensure all interactive nodes (buttons, inputs) include micro-animations (transitions, hovers).
- Prefer dark modes or support seamless theme switching.

## 3. Web Standards & a11y
- Always use semantic HTML tags (`<header>`, `<nav>`, `<main>`, `<article>`, `<footer>`).
- Ensure proper accessibility labels (`aria-label`) on all icon buttons and visual elements.
- Never use `localStorage` for state caching (use `sessionStorage` or `indexedDB` to satisfy security gates).
