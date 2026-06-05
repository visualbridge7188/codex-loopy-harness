#!/bin/bash
# PostToolUse Hook: Scans database tool inputs for SQL injection patterns.
# Exit code 2 blocks execution.

set -euo pipefail

TOOL_INPUT="${TOOL_INPUT:-}"

# Check for typical SQLi indicators
SQL_INJECTION_PATTERNS=(
  "union[[:space:]]+select"
  "or[[:space:]]+1[[:space:]]*=[[:space:]]*1"
  "or[[:space:]]+'1'[[:space:]]*=[[:space:]]*'1'"
  "--"
  ";[[:space:]]*drop"
  ";[[:space:]]*truncate"
)

LOWER_INPUT=$(echo "$TOOL_INPUT" | tr '[:upper:]' '[:lower:]')

for pattern in "${SQL_INJECTION_PATTERNS[@]}"; do
  if echo "$LOWER_INPUT" | grep -qE "$pattern"; then
    echo "❌ [sql-injection-check] Blocked: Potential SQL injection pattern detected ('$pattern')."
    exit 2
  fi
done

exit 0
