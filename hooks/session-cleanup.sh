#!/bin/bash
# Stop Event Hook: Run system cleanup operations on session stop.

set -euo pipefail

CWD="${CWD:-$(pwd)}"

echo "🧹 Cleaning up session runtime folders..."

# Remove lockfiles and temp files
rm -f "$CWD"/.git/index.lock 2>/dev/null || true
rm -f /tmp/validation-error.log 2>/dev/null || true

# Keep harness status clean
echo "✓ Session cleanup completed."
exit 0
