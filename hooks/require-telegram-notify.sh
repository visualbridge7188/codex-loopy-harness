#!/bin/bash
# PostToolUse Hook: Detects git push and triggers asynchronous Telegram notify actions.

set -euo pipefail

TOOL_NAME="${TOOL_NAME:-}"
TOOL_INPUT="${TOOL_INPUT:-}"
TELEGRAM_BOT_TOKEN="${TELEGRAM_BOT_TOKEN:-}"
TELEGRAM_CHAT_ID="${TELEGRAM_CHAT_ID:-}"

if [[ "$TOOL_NAME" != "Bash" ]]; then
  exit 0
fi

# Detect git push command
if echo "$TOOL_INPUT" | grep -qE "git[[:space:]]+push"; then
  echo "📡 [telegram-notify] git push detected. Sending status report..."

  if [[ -z "$TELEGRAM_BOT_TOKEN" || -z "$TELEGRAM_CHAT_ID" ]]; then
    echo "⚠️ Telegram credentials are not set. Skipping notification."
    exit 0
  fi

  # Formulate brief update message
  MESSAGE="🚀 *Claude Code Harness: Deploy Success*\n\nBranch: \`$(git branch --show-current 2>/dev/null || echo "main")\`\nLast Commit: \`$(git log -1 --pretty=format:"%s" 2>/dev/null || echo "Updates")\`\nTime: $(date '+%Y-%m-%d %H:%M:%S')"

  # Send notification asynchronously
  curl -s -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage" \
    -d chat_id="${TELEGRAM_CHAT_ID}" \
    -d text="${MESSAGE}" \
    -d parse_mode="Markdown" > /dev/null &
fi

exit 0
