#!/usr/bin/env python3
# <bitbar.title>AI Tools Total Monitor</bitbar.title>
# <bitbar.version>v1.0</bitbar.version>
# <bitbar.author>User</bitbar.author>
# <bitbar.desc>Monitors Codex, Antigravity, and Z AI tokens natively</bitbar.desc>

import os
import json
import subprocess
import urllib.request
import urllib.error

# Prepend standard binary search paths for SwiftBar environment
extra_paths = ["/usr/local/bin", "/opt/homebrew/bin", os.path.expanduser("~/.codex/bin")]
for path in extra_paths:
    if path not in os.environ.get("PATH", ""):
        os.environ["PATH"] = f"{path}:{os.environ.get('PATH', '')}"

CONFIG_FILE = os.path.join(os.path.dirname(__file__), 'config.json')

def load_config():
    if not os.path.exists(CONFIG_FILE):
        default_config = {
            "Z_AI_API_KEY": "YOUR_API_KEY_HERE"
        }
        with open(CONFIG_FILE, 'w') as f:
            json.dump(default_config, f, indent=4)
        return default_config
    
    with open(CONFIG_FILE, 'r') as f:
        return json.load(f)

def format_time(seconds):
    mins = seconds // 60
    secs = seconds % 60
    return f"{mins}m {secs}s"

def get_codex_data():
    try:
        result = subprocess.run(["codex", "status", "--json"], capture_output=True, text=True, check=True)
        data = json.loads(result.stdout)
        tokens = data.get("tokens_remaining", 0)
        limit = data.get("tokens_limit", 100000)
        reset_sec = data.get("reset_in_seconds", 0)
        
        percent = int(tokens * 100 / limit) if limit > 0 else 0
        formatted_tokens = f"{tokens // 1000}k" if tokens >= 1000 else str(tokens)
        alert = percent < 10
        
        return {
            "percent": percent,
            "remaining": formatted_tokens,
            "reset": format_time(reset_sec),
            "alert": alert,
            "error": None,
            "mocked": False
        }
    except Exception as e:
        return {
            "percent": 45,
            "remaining": "45k",
            "reset": "12m 30s",
            "alert": False,
            "error": f"CLI Error: {str(e)} (Using simulated mock data)",
            "mocked": True
        }

def get_antigravity_data():
    try:
        result = subprocess.run(["antigravity-usage", "--status", "json"], capture_output=True, text=True, check=True)
        data = json.loads(result.stdout)
        quota = data.get("model_quota", {}).get("gemini-pro", "0%")
        
        quota_val = int(quota.replace("%", "")) if "%" in quota else 0
        alert = quota_val < 10
        
        return {
            "percent": quota_val,
            "remaining": quota,
            "reset": "check CLI",
            "alert": alert,
            "error": None,
            "mocked": False
        }
    except Exception as e:
        return {
            "percent": 82,
            "remaining": "82%",
            "reset": "1h 05m",
            "alert": False,
            "error": f"CLI Error: {str(e)} (Using simulated mock data)",
            "mocked": True
        }

def get_zai_data(api_key):
    if not api_key or api_key == "YOUR_API_KEY_HERE":
        return {
            "percent": 85,
            "remaining": "85%",
            "reset": "18:00",
            "alert": False,
            "error": "No API Key configured (Using simulated mock data)",
            "mocked": True
        }
        
    try:
        req = urllib.request.Request(
            "https://api.z.ai/api/monitor/usage/quota/limit",
            headers={"Authorization": f"Bearer {api_key}", "Accept": "application/json"}
        )
        with urllib.request.urlopen(req, timeout=3) as response:
            data = json.loads(response.read())
            
        limits = data.get("data", {}).get("limits", [])
        if not limits:
            raise Exception("No limits data in response")
            
        limit = limits[0]
        remaining = limit.get("remaining", 100)
        percentage = limit.get("percentage", 0)
        
        remaining_percent = 100 - percentage
        alert = remaining_percent < 10
        
        reset_ms = limit.get("nextResetTime")
        reset_str = "?"
        if reset_ms:
            import datetime
            reset_str = datetime.datetime.fromtimestamp(reset_ms / 1000).strftime('%H:%M')
            
        return {
            "percent": remaining_percent,
            "remaining": f"{remaining_percent}%",
            "reset": reset_str,
            "alert": alert,
            "error": None,
            "mocked": False
        }
    except Exception as e:
        return {
            "percent": 85,
            "remaining": "85%",
            "reset": "18:00",
            "alert": False,
            "error": f"API Error: {str(e)} (Using simulated mock data)",
            "mocked": True
        }

def get_bar(percentage):
    filled = min(5, max(0, round(percentage / 20)))
    empty = 5 - filled
    return "█" * filled + "░" * empty

def render_ui(codex, ag, zai):
    cx_pct = codex['percent']
    ag_pct = ag['percent']
    zai_pct = zai['percent']
    
    cx_bar = get_bar(cx_pct)
    ag_bar = get_bar(ag_pct)
    zai_bar = get_bar(zai_pct)
    
    title_line1 = f"🤖 {cx_pct}% │ 🚀 {ag_pct}% │ ⚡️ {zai_pct}%"
    title_line2 = f"{cx_bar} │ {ag_bar} │ {zai_bar}"
    
    any_alert = codex['alert'] or ag['alert'] or zai['alert']
    
    # Print the multi-line menu bar title.
    # SwiftBar cycles lines before the first '---'. We apply styling parameters.
    param = " | size=10"
    if any_alert:
        param += " color=red"
        
    print(f"{title_line1}\n{title_line2}{param}")
    print("---")
    
    # Dropdown Details
    print(f"🤖 Codex: {codex['remaining']} ({cx_pct}%) - Reset in {codex['reset']}")
    if codex['error']: print(f"   {codex['error']}")
    print("---")
    
    print(f"🚀 Antigravity: {ag['remaining']} ({ag_pct}%) - Reset in {ag['reset']}")
    if ag['error']: print(f"   {ag['error']}")
    print("---")
    
    print(f"⚡️ Z AI: {zai['remaining']} ({zai_pct}%) - Next Reset: {zai['reset']}")
    if zai['error']: print(f"   {zai['error']}")
    print("---")
    
    print("🔄 Force Refresh Data | refresh=true")

if __name__ == "__main__":
    config = load_config()
    
    codex_data = get_codex_data()
    ag_data = get_antigravity_data()
    zai_data = get_zai_data(config.get("Z_AI_API_KEY", ""))
    
    render_ui(codex_data, ag_data, zai_data)
