#!/bin/bash
# post-tool-use-tracker.sh
#
# PostToolUse hook that tracks edited files and their contexts.
# Runs after Edit, Write, or MultiEdit tools complete successfully.
# Creates per-session cache for build verification and context management.
#
# Adapted from: jung-wan-kim/claude-code-infrastructure-showcase

set -e

# Read tool information from stdin
tool_info=$(cat)

# Extract relevant data
tool_name=$(echo "$tool_info" | jq -r '.tool_name // empty' 2>/dev/null)
file_path=$(echo "$tool_info" | jq -r '.tool_input.file_path // empty' 2>/dev/null)
session_id=$(echo "$tool_info" | jq -r '.session_id // empty' 2>/dev/null)

# Skip if not an edit tool or no file path
if [[ ! "$tool_name" =~ ^(Edit|MultiEdit|Write|replace_in_file|write_to_file)$ ]] || [[ -z "$file_path" ]]; then
    exit 0
fi

# Skip non-code files
if [[ "$file_path" =~ \.(md|markdown|txt|json|yaml|yml|css|scss|svg|png|jpg|gif|ico)$ ]]; then
    exit 0
fi

# Create cache directory
cache_dir="${CLAUDE_PROJECT_DIR:-$(pwd)}/.claude-cache/${session_id:-default}"
mkdir -p "$cache_dir"

# Function to detect project area from file path
detect_area() {
    local file="$1"
    local project_root="${CLAUDE_PROJECT_DIR:-$(pwd)}"
    local relative_path="${file#$project_root/}"

    # Extract first directory component
    local area=$(echo "$relative_path" | cut -d'/' -f1)

    case "$area" in
        # Frontend areas
        src|frontend|client|web|app|ui|components|pages)
            echo "frontend:$area"
            ;;
        # Backend areas
        api|server|backend|services|routes|controllers)
            echo "backend:$area"
            ;;
        # Database
        database|db|prisma|migrations|supabase)
            echo "database:$area"
            ;;
        # Infrastructure
        infra|docker|k8s|.github|scripts|hooks)
            echo "infra:$area"
            ;;
        # Tests
        tests|test|__tests__|spec|e2e)
            echo "test:$area"
            ;;
        # Docs
        docs|documentation)
            echo "docs:$area"
            ;;
        *)
            if [[ ! "$relative_path" =~ / ]]; then
                echo "root:$area"
            else
                echo "other:$area"
            fi
            ;;
    esac
}

# Function to detect file type
detect_file_type() {
    local file="$1"
    case "$file" in
        *.tsx|*.jsx) echo "component" ;;
        *.ts|*.js) echo "script" ;;
        *.py) echo "python" ;;
        *.sql) echo "sql" ;;
        *.sh) echo "shell" ;;
        *.css|*.scss|*.less) echo "style" ;;
        *.html) echo "html" ;;
        *.json) echo "config" ;;
        *) echo "unknown" ;;
    esac
}

# Detect area and file type
area=$(detect_area "$file_path")
file_type=$(detect_file_type "$file_path")

# Log the edit with timestamp and metadata
timestamp=$(date +%s)
echo "${timestamp}:${file_path}:${area}:${file_type}:${tool_name}" >> "$cache_dir/edit-log.tsv"

# Update affected areas list (deduplicated)
area_name=$(echo "$area" | cut -d':' -f2)
if ! grep -q "^${area_name}$" "$cache_dir/affected-areas.txt" 2>/dev/null; then
    echo "$area_name" >> "$cache_dir/affected-areas.txt"
fi

# Track file types edited
if ! grep -q "^${file_type}$" "$cache_dir/file-types.txt" 2>/dev/null; then
    echo "$file_type" >> "$cache_dir/file-types.txt"
fi

# Count edits per area for priority scoring
edit_count_file="$cache_dir/edit-counts.txt"
current_count=$(grep "^${area_name}:" "$edit_count_file" 2>/dev/null | cut -d':' -f2)
new_count=$(( (${current_count:-0}) + 1 ))
# Update count (use temp file for atomic update)
grep -v "^${area_name}:" "$edit_count_file" 2>/dev/null > "$edit_count_file.tmp" || true
echo "${area_name}:${new_count}" >> "$edit_count_file.tmp"
mv "$edit_count_file.tmp" "$edit_count_file" 2>/dev/null || true

# Check for potential issues
warnings=""

# Check for large number of edits in single area (possible refactoring needed)
if [[ $new_count -gt 10 ]]; then
    warnings="${warnings}\n⚠️  High edit count in ${area_name} (${new_count} edits) — consider refactoring"
fi

# Check if editing both frontend and backend simultaneously
if grep -q "^frontend:" "$cache_dir/affected-areas.txt" 2>/dev/null && \
   grep -q "^backend:" "$cache_dir/affected-areas.txt" 2>/dev/null; then
    warnings="${warnings}\n⚠️  Editing both frontend and backend — ensure API contracts are aligned"
fi

# Output warnings if any
if [[ -n "$warnings" ]]; then
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "📊 EDIT TRACKER WARNING"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo -e "$warnings"
    echo ""
    echo "Session edits: $(wc -l < "$cache_dir/edit-log.tsv") files"
    echo "Areas affected: $(wc -l < "$cache_dir/affected-areas.txt")"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
fi

exit 0