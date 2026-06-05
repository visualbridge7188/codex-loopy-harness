#!/bin/bash
# PostToolUse Hook: Blocks any use of localStorage to prevent persistent client-side states.
# Exit code 2 blocks the tool execution.

set -euo pipefail

TOOL_NAME="${TOOL_NAME:-}"
TOOL_INPUT="${TOOL_INPUT:-}"

if [[ "$TOOL_NAME" != "Edit" && "$TOOL_NAME" != "Write" ]]; then
  exit 0
fi

# Detect localStorage references in tool inputs
if echo "$TOOL_INPUT" | grep -qi "localStorage"; then
  echo "❌ [no-localstorage] Blocked: Use of localStorage is strictly forbidden in this harness. Use sessionStorage or indexedDB instead."
  exit 2
fi

exit 0
