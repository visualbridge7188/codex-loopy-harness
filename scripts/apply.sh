#!/bin/bash
# Apply template configurations from local workspace repository to Claude Code scope
# Direction: Workspace Repository -> ~/.claude/

set -euo pipefail

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$( cd "$SCRIPT_DIR/.." && pwd )"
TARGET_DIR="$HOME/.claude"

sync_dir() {
    local src="$1" dst="$2"
    if [ -d "$src" ]; then
        mkdir -p "$dst"
        cp -r "$src"/* "$dst"/ 2>/dev/null || true
        rm -rf "$dst/.git" 2>/dev/null || true
    fi
}

echo "📦 Claude Code 설정 적용 중..."
echo "Source: $PROJECT_ROOT"
echo "Target: $TARGET_DIR"
echo ""

mkdir -p "$TARGET_DIR"

# Copy directories
for dir in agents hooks skills memory plugins commands scripts rules teams; do
    if [ -d "$PROJECT_ROOT/$dir" ]; then
        echo "✓ $dir 복사 중..."
        sync_dir "$PROJECT_ROOT/$dir" "$TARGET_DIR/$dir"
    fi
done

# Copy individual files
for file in settings.json CLAUDE.md keybindings.json claude_desktop_config.json settings.local.json claude_code_config.json; do
    if [ -f "$PROJECT_ROOT/$file" ]; then
        echo "✓ $file 복사 중..."
        cp "$PROJECT_ROOT/$file" "$TARGET_DIR/"
    fi
done

# Set permissions for hooks and scripts
echo "✓ 실행 권한 설정 중..."
chmod +x "$TARGET_DIR/hooks"/*.sh 2>/dev/null || true
chmod +x "$TARGET_DIR/scripts"/*.sh 2>/dev/null || true

echo ""
echo "✅ 설정 적용 완료!"
echo "💡 Claude Code를 재시작하려면 다음을 실행하세요: claude restart"
