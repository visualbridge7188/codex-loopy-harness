#!/bin/bash
# PrePush Hook: Run compilation and test suite checks before git push.
# Exit code 2 blocks git push.

set -euo pipefail

CWD="${CWD:-$(pwd)}"

echo "🧪 Running pre-push QA verification gates..."

if [ -f "$CWD/package.json" ]; then
  # 1. Type check
  if [ -d "$CWD/node_modules" ]; then
    echo "✓ Running TypeScript check..."
    if ! npx tsc --noEmit; then
      echo "❌ [pre-push-qa] TypeScript checks failed. Resolve compile errors before pushing."
      exit 2
    fi

    # 2. Test runner
    if grep -q "test" "$CWD/package.json"; then
      echo "✓ Running test suites..."
      if ! npm test; then
        echo "❌ [pre-push-qa] Test runner reported failures. Resolve failing test cases before pushing."
        exit 2
      fi
    fi
  fi
fi

echo "✅ [pre-push-qa] All quality gates passed successfully."
exit 0
