#!/bin/bash
# Sync active Claude Code configurations back to the local workspace template repo
# Direction: ~/.claude/ -> Workspace Repository

set -euo pipefail

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$( cd "$SCRIPT_DIR/.." && pwd )"
SOURCE_DIR="$HOME/.claude"

sync_dir() {
    local src="$1" dst="$2"
    if [ -d "$src" ]; then
        mkdir -p "$dst"
        # Copy everything except hidden files (like .git)
        cp -r "$src"/* "$dst"/ 2>/dev/null || true
        rm -rf "$dst/.git" 2>/dev/null || true
    fi
}

echo "🔄 Claude Code 설정 동기화 중..."
echo "Source: $SOURCE_DIR"
echo "Target: $PROJECT_ROOT"
echo ""

# Sync directories
for dir in agents hooks skills memory plugins commands scripts rules teams; do
    if [ -d "$SOURCE_DIR/$dir" ]; then
        echo "✓ $dir 동기화 중..."
        sync_dir "$SOURCE_DIR/$dir" "$PROJECT_ROOT/$dir"
    fi
done

# Sync individual files
for file in settings.json CLAUDE.md keybindings.json claude_desktop_config.json settings.local.json claude_code_config.json; do
    if [ -f "$SOURCE_DIR/$file" ]; then
        echo "✓ $file 동기화 중..."
        cp "$SOURCE_DIR/$file" "$PROJECT_ROOT/"
    fi
done

echo ""
echo "✅ 동기화 완료!"
