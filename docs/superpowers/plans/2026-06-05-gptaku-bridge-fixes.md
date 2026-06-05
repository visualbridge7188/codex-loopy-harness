# Gptaku Bridge Fixes Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Fix critical exit code bug, security shell injection vector, and introduce automated integration testing in the workspace.

**Architecture:** Refactor `scripts/install-gptaku.mjs` to validate URLs via regex and safely manage exit codes without skipping directory cleanups. Add a Node.js native test suite to verify script execution exit codes.

**Tech Stack:** Node.js (v22.19.0), ES Modules, `child_process`, native `node:test` runner.

---

### Task 1: URL Validation and Exit Code Handling in `install-gptaku.mjs`

**Files:**
- Modify: `scripts/install-gptaku.mjs:1-125`

- [x] **Step 1: Define validation regex and exit code tracking**

Add URL regex checks and track the exit status in `scripts/install-gptaku.mjs` to ensure clean exit and safe parameters.

Code to add:
```javascript
const gitUrlRegex = /^(https?:\/\/|git@)[a-zA-Z0-9.-]+[:/][a-zA-Z0-9._-]+\/[a-zA-Z0-9._-]+(\.git)?$/;
if (!gitUrlRegex.test(repoUrl)) {
  console.error('Error: Invalid Git repository URL format.');
  process.exit(1);
}
```

Refactor `main` to track `exitCode` and call `process.exit(exitCode)` at the very end to prevent bypassing the `finally` cleanup:
```javascript
let exitCode = 0;
async function main() {
  try {
    // ... logic ...
  } catch (error) {
    console.error('Installation failed:', error.message);
    exitCode = 1;
  } finally {
    try {
      await fs.rm(tempDir, { recursive: true, force: true });
    } catch {}
  }
  if (exitCode !== 0) {
    process.exit(exitCode);
  }
}
```

- [x] **Step 2: Commit changes**

Run:
```bash
git add scripts/install-gptaku.mjs
git commit -m "fix: add git URL validation and proper exit code handling"
```

---

### Task 2: Automated Integration Tests

**Files:**
- Create: `test/install-gptaku.test.mjs`

- [x] **Step 1: Create test suite verifying exit codes and validations**

Write test code executing the script as a subprocess and asserting correct exit codes for invalid inputs and failures.

Code:
```javascript
import { execSync } from 'child_process';
import test from 'node:test';
import assert from 'node:assert';

test('install-gptaku.mjs exit codes and validation', async (t) => {
  await t.test('fails when no arguments are provided', () => {
    assert.throws(() => {
      execSync('node scripts/install-gptaku.mjs', { stdio: 'pipe' });
    }, (err) => {
      return err.status === 1;
    });
  });

  await t.test('fails when invalid URL is provided', () => {
    assert.throws(() => {
      execSync('node scripts/install-gptaku.mjs "not-a-valid-url"', { stdio: 'pipe' });
    }, (err) => {
      return err.status === 1;
    });
  });

  await t.test('fails when cloning non-existent repo', () => {
    assert.throws(() => {
      execSync('node scripts/install-gptaku.mjs "https://github.com/fivetaku/non-existent-repo-12345.git"', { stdio: 'pipe' });
    }, (err) => {
      return err.status === 1;
    });
  });
});
```

- [x] **Step 2: Run tests to verify they pass**

Run: `node --test test/install-gptaku.test.mjs`
Expected: All 3 tests pass.

- [x] **Step 3: Commit tests**

Run:
```bash
git add test/install-gptaku.test.mjs
git commit -m "test: add integration test suite for install-gptaku script"
```
