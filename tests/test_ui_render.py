import unittest
import importlib.util
from io import StringIO
from unittest.mock import patch

spec = importlib.util.spec_from_file_location("ai_status", "ai_status.5m.py")
ai_status = importlib.util.module_from_spec(spec)
spec.loader.exec_module(ai_status)

class TestUIRender(unittest.TestCase):
    @patch('sys.stdout', new_callable=StringIO)
    def test_render_output(self, mock_stdout):
        codex = {"percent": 45, "remaining": "45k", "reset": "12m", "alert": False, "error": None}
        ag = {"percent": 82, "remaining": "82%", "reset": "1h", "alert": True, "error": None}
        zai = {"percent": 85, "remaining": "85", "reset": "18:00", "alert": False, "error": None}
        
        ai_status.render_ui(codex, ag, zai)
        
        output = mock_stdout.getvalue()
        self.assertIn("🤖 45% │ 🚀 82% │ ⚡️ 85%\n██░░░ │ ████░ │ ████░ | size=10 color=red", output)
        self.assertIn("refresh=true", output)

if __name__ == '__main__':
    unittest.main()
