#!/bin/bash
# skill-activation-prompt.sh
# UserPromptSubmit hook: Auto-suggests skills based on user prompts
# Reads JSON input from stdin, pipes to tsx for TypeScript processing

set -e

# Try tsx first, fallback to node with ts-node
if command -v npx &> /dev/null; then
    npx tsx "$(dirname "$0")/skill-activation-prompt.ts" 2>/dev/null
elif command -v tsx &> /dev/null; then
    tsx "$(dirname "$0")/skill-activation-prompt.ts" 2>/dev/null
else
    # Fallback: simple bash-based keyword matching
    input=$(cat)
    prompt=$(echo "$input" | jq -r '.prompt // empty' 2>/dev/null | tr '[:upper:]' '[:lower:]')

    if [[ -z "$prompt" ]]; then
        exit 0
    fi

    # Check for critical skill keywords
    skills_dir="${CLAUDE_PROJECT_DIR:-$(pwd)}/skills"
    rules_file="$skills_dir/skill-rules.json"

    if [[ ! -f "$rules_file" ]]; then
        exit 0
    fi

    # Simple keyword extraction from skill-rules.json
    matched=""
    while IFS='=' read -r skill keywords; do
        for kw in $(echo "$keywords" | tr ',' ' '); do
            if [[ "$prompt" == *"$kw"* ]]; then
                matched="$matched\n  → $skill"
                break
            fi
        done
    done < <(jq -r '.skills | to_entries[] | "\(.key)=\(.value.promptTriggers.keywords | join(","))"' "$rules_file" 2>/dev/null)

    if [[ -n "$matched" ]]; then
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        echo "🎯 SKILL ACTIVATION CHECK"
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        echo ""
        echo "📚 MATCHED SKILLS:"
        echo -e "$matched"
        echo ""
        echo "ACTION: Use Skill tool BEFORE responding"
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    fi
fi

exit 0