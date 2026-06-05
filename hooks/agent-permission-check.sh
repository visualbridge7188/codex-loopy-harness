#!/bin/bash
# PostToolUse Hook: Enforces agent boundaries by blocking Manager-Orchestrator from editing src files.
# Exit code 2 blocks the tool execution.

set -euo pipefail

TOOL_NAME="${TOOL_NAME:-}"
TOOL_INPUT="${TOOL_INPUT:-}"

if [[ "$TOOL_NAME" != "Edit" && "$TOOL_NAME" != "Write" ]]; then
  exit 0
fi

# Extract the target path from the TOOL_INPUT JSON
# TOOL_INPUT is typically: {"path":"src/components/Button.tsx", "content":...}
TARGET_PATH=$(echo "$TOOL_INPUT" | grep -oE '"path"\s*:\s*"[^"]+"' | head -1 | cut -d'"' -f4 || true)

if [[ -z "$TARGET_PATH" ]]; then
  # Fallback: check if the string contains src
  if echo "$TOOL_INPUT" | grep -q '"path"'; then
    TARGET_PATH=$(echo "$TOOL_INPUT" | sed -n 's/.*"path"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p')
  fi
fi

# Enforce boundary: if editing production files, require specialist role assertion
if [[ "$TARGET_PATH" =~ ^src/ ]]; then
  # Check if the execution context declares a Specialist role (must be documented in context)
  # In our framework, the Manager agent MUST NOT edit files in src/ directly.
  # We check the AGENT_ROLE env variable (passed in the prompt context or settings.json)
  AGENT_ROLE="${AGENT_ROLE:-manager}"
  
  if [ "$AGENT_ROLE" = "manager" ] || [ "$AGENT_ROLE" = "manager-orchestrator" ]; then
    echo "❌ [agent-permission-check] Blocked: Manager-Orchestrator cannot modify files in src/ directly. Please delegate this to a specialist subagent."
    exit 2
  fi
fi

exit 0
