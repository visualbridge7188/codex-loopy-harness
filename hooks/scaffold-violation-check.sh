#!/bin/bash
# PostToolUse Hook: Enforces project-specific NEVER DO rules defined in SKILL.md.
# Exit code 2 blocks execution.

set -euo pipefail

TOOL_NAME="${TOOL_NAME:-}"
TOOL_INPUT="${TOOL_INPUT:-}"
CWD="${CWD:-$(pwd)}"

if [[ "$TOOL_NAME" != "Edit" && "$TOOL_NAME" != "Write" ]]; then
  exit 0
fi

# Locate the project's living rules file
RULES_FILE="$CWD/SKILL.md"
if [ ! -f "$RULES_FILE" ]; then
  # Try to find {project}-scaffold/SKILL.md
  RULES_FILE=$(find "$CWD" -name "SKILL.md" | head -n 1 || true)
fi

if [ -n "$RULES_FILE" ] && [ -f "$RULES_FILE" ]; then
  # Parse NEVER DO patterns from the SKILL.md file
  # Look for lines containing "NEVER DO:" or bullet points under NEVER DO headers
  NEVER_DO_RULES=$(grep -i "NEVER DO" "$RULES_FILE" -A 10 | grep -E "^[[:space:]]*- " | sed 's/^[[:space:]]*- //g' || true)

  if [ -n "$NEVER_DO_RULES" ]; then
    # Iterate through extracted NEVER DO rules and verify tool input doesn't contain them
    IFS=$'\n'
    for rule in $NEVER_DO_RULES; do
      # If the rule is not empty and is found in the tool edit input, block it
      if [ -n "$rule" ] && echo "$TOOL_INPUT" | grep -qiF "$rule"; then
        echo "❌ [scaffold-violation-check] Blocked: Rule violation ('$rule') from project SKILL.md ruleset."
        exit 2
      fi
    done
  fi
fi

exit 0
