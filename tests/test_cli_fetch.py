import unittest
from unittest.mock import patch, MagicMock
import importlib.util

spec = importlib.util.spec_from_file_location("ai_status", "ai_status.5m.py")
ai_status = importlib.util.module_from_spec(spec)
spec.loader.exec_module(ai_status)

class TestCliFetch(unittest.TestCase):
    @patch('subprocess.run')
    def test_get_codex(self, mock_run):
        mock_result = MagicMock()
        mock_result.stdout = '{"tokens_remaining": 45000, "reset_in_seconds": 720}'
        mock_run.return_value = mock_result
        
        result = ai_status.get_codex_data()
        self.assertEqual(result['remaining'], "45k")
        self.assertEqual(result['reset'], "12m 0s")
        self.assertFalse(result['alert'])

    @patch('subprocess.run')
    def test_get_antigravity(self, mock_run):
        mock_result = MagicMock()
        mock_result.stdout = '{"model_quota": {"gemini-pro": "82%"}, "next_reset_epoch": 0}'
        mock_run.return_value = mock_result
        
        result = ai_status.get_antigravity_data()
        self.assertEqual(result['remaining'], "82%")
        self.assertFalse(result['alert'])

if __name__ == '__main__':
    unittest.main()
