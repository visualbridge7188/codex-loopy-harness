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
        reset_sec = data.get("reset_in_seconds", 0)
        
        # simple format: 45000 -> 45k
        formatted_tokens = f"{tokens // 1000}k" if tokens >= 1000 else str(tokens)
        alert = tokens < 5000 # Alert if less than 5k tokens
        
        return {
            "remaining": formatted_tokens,
            "reset": format_time(reset_sec),
            "alert": alert,
            "error": None
        }
    except Exception as e:
        return {"remaining": "?", "reset": "?", "alert": True, "error": str(e)}

def get_antigravity_data():
    try:
        result = subprocess.run(["antigravity-usage", "--status", "json"], capture_output=True, text=True, check=True)
        data = json.loads(result.stdout)
        quota = data.get("model_quota", {}).get("gemini-pro", "0%")
        
        # parse percent value for alert
        quota_val = int(quota.replace("%", "")) if "%" in quota else 0
        alert = quota_val < 10 # Alert if less than 10%
        
        return {
            "remaining": quota,
            "reset": "check CLI",
            "alert": alert,
            "error": None
        }
    except Exception as e:
        return {"remaining": "?", "reset": "?", "alert": True, "error": str(e)}

def get_zai_data(api_key):
    if not api_key or api_key == "YOUR_API_KEY_HERE":
        return {"remaining": "?", "reset": "?", "alert": True, "error": "Invalid API Key"}
        
    try:
        req = urllib.request.Request(
            "https://api.z.ai/api/monitor/usage/quota/limit",
            headers={"Authorization": f"Bearer {api_key}", "Accept": "application/json"}
        )
        with urllib.request.urlopen(req, timeout=3) as response:
            data = json.loads(response.read())
            
        remaining = data.get("data", {}).get("remaining_requests", 0)
        reset_time = data.get("data", {}).get("next_reset_time", "?")
        
        alert = int(remaining) < 10
        
        return {
            "remaining": str(remaining),
            "reset": reset_time,
            "alert": alert,
            "error": None
        }
    except Exception as e:
        return {"remaining": "?", "reset": "?", "alert": True, "error": str(e)}

if __name__ == "__main__":
    config = load_config()
