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
        codex = {"remaining": "45k", "reset": "12m", "alert": False, "error": None}
        ag = {"remaining": "82%", "reset": "1h", "alert": True, "error": None}
        zai = {"remaining": "85", "reset": "18:00", "alert": False, "error": None}
        
        ai_status.render_ui(codex, ag, zai)
        
        output = mock_stdout.getvalue()
        # Should render combined single line for menu bar with warning symbols and overall color
        self.assertIn("🤖 CX: 45k │ 🚀⚠️ AG: 82% │ ⚡️ Z: 85 | color=red", output)
        self.assertIn("refresh=true", output)

if __name__ == '__main__':
    unittest.main()
