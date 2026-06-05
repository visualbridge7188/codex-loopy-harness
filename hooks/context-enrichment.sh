#!/bin/bash
# UserPromptSubmit Hook: Injects continuity context (errors, pending tasks) into the prompt.
# Claude Code prepends the stdout of this script to the user's prompt.

set -euo pipefail

CWD="${CWD:-$(pwd)}"

# Read user prompt from stdin
USER_PROMPT=""
if [ ! -t 0 ]; then
  USER_PROMPT=$(cat 2>/dev/null || true)
fi

CONTEXT=""

# 1. Inject validation logs if they exist and are non-empty
ERROR_LOG="/tmp/validation-error.log"
if [ -f "$ERROR_LOG" ] && [ -s "$ERROR_LOG" ]; then
  CONTEXT="${CONTEXT}[CONTEXT] Recent build/validation errors detected:\n\`\`\`\n$(cat "$ERROR_LOG" | tail -n 20)\n\`\`\`\nPlease address these compile/test errors first.\n\n"
fi

# 2. Inject pending self-improve rules if they exist
PENDING_DIR="$HOME/.claude/pending"
if [ -d "$PENDING_DIR" ]; then
  PENDING_FILES=$(find "$PENDING_DIR" -name "self-improve-*.json" 2>/dev/null || true)
  if [ -n "$PENDING_FILES" ]; then
    CONTEXT="${CONTEXT}[CONTEXT] Pending self-improvement updates:\n"
    for f in $PENDING_FILES; do
      CONTEXT="${CONTEXT}- \$(basename "\$f"): \$(grep -o '"rules":[^}]*' "\$f" | cut -d: -f2- || echo 'Scaffold updates pending')\n"
    done
    CONTEXT="${CONTEXT}Run /self-improve to apply pending updates and reinforce NEVER DO rules.\n\n"
  fi
fi

# Output prepended context block
if [ -n "$CONTEXT" ]; then
  printf "%b" "$CONTEXT"
fi

exit 0
