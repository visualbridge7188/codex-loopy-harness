#!/bin/bash
# PostToolUse Hook: Runs automated typescript check and linter on edits.
# Exit code 2 blocks the tool execution and forces cleanup/fix phase.

set -euo pipefail

TOOL_NAME="${TOOL_NAME:-}"
CWD="${CWD:-$(pwd)}"

if [[ "$TOOL_NAME" != "Edit" && "$TOOL_NAME" != "Write" ]]; then
  exit 0
fi

echo "🔍 Running auto-validate checks..."

# Check if npm dependencies and package.json exist before checking
if [ -f "$CWD/package.json" ] && [ -d "$CWD/node_modules" ]; then
  # 1. Typecheck (tsc)
  if grep -q "build" "$CWD/package.json"; then
    echo "✓ Verifying build status..."
    if ! npm run build --dry-run 2>/dev/null; then
      # If dry-run fails, run actual typecheck
      if ! npx tsc --noEmit; then
        echo "❌ [auto-validate] TypeScript verification failed."
        exit 2
      fi
    fi
  fi

  # 2. Linting
  if grep -q "lint" "$CWD/package.json"; then
    echo "✓ Verifying lint guidelines..."
    if ! npm run lint -- --max-warnings=0 2>/dev/null; then
      echo "⚠️ [auto-validate] Linter violations found."
      # We warn instead of hard block here (allow soft rules unless critical)
    fi
  fi
fi

exit 0
