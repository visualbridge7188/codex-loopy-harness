import unittest
from unittest.mock import patch, MagicMock
import importlib.util

spec = importlib.util.spec_from_file_location("ai_status", "ai_status.5m.py")
ai_status = importlib.util.module_from_spec(spec)
spec.loader.exec_module(ai_status)

class TestHttpFetch(unittest.TestCase):
    @patch('urllib.request.urlopen')
    def test_get_zai(self, mock_urlopen):
        mock_response = MagicMock()
        mock_response.read.return_value = b'{"data": {"remaining_requests": 85, "next_reset_time": "18:00"}}'
        mock_urlopen.return_value.__enter__.return_value = mock_response
        
        result = ai_status.get_zai_data("fake_key")
        self.assertEqual(result['remaining'], "85")
        self.assertEqual(result['reset'], "18:00")
        self.assertFalse(result['alert'])

if __name__ == '__main__':
    unittest.main()
