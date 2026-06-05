#!/bin/bash
# PostToolUse Hook: Blocks frontend layout edits if they bypass PWA configurations.
# Exit code 2 blocks the tool execution.

set -euo pipefail

TOOL_NAME="${TOOL_NAME:-}"
TOOL_INPUT="${TOOL_INPUT:-}"

if [[ "$TOOL_NAME" != "Edit" && "$TOOL_NAME" != "Write" ]]; then
  exit 0
fi

# If editing components or pages, verify responsiveness/PWA conditions
if echo "$TOOL_INPUT" | grep -qE "src/pages/|src/components/"; then
  # Verify if layout components contain responsive wrappers or isPWA checks
  # (Simulated check: search for viewport, media queries, or PWA-specific conditions if changing styling/layout)
  if echo "$TOOL_INPUT" | grep -qiE "media[[:space:]]+query|@media|layout|viewports"; then
    if ! echo "$TOOL_INPUT" | grep -qiE "isPWA|responsive|breakpoint"; then
      echo "❌ [require-isPWA-check] Blocked: Layout edits must include responsive wrappers or PWA-check branches."
      exit 2
    fi
  fi
fi

exit 0
