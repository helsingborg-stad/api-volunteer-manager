import json
import tempfile
import unittest
from pathlib import Path

import set_release_version


class ReleaseVersionTest(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.root = Path(self.temp.name)
        (self.root / "package.json").write_text('{\n  "version": "0.5.1"\n}\n')
        (self.root / "package-lock.json").write_text(
            '{\n  "version": "0.5.1",\n  "packages": {"": {"version": "0.5.1"}}\n}\n'
        )
        (self.root / "api-volunteer-manager.php").write_text("<?php\n/**\n * Version: 0.5.1\n */\n")

    def tearDown(self):
        self.temp.cleanup()

    def test_updates_all_version_fields(self):
        set_release_version.update(self.root, "0.5.2")
        self.assertEqual(json.loads((self.root / "package.json").read_text())["version"], "0.5.2")
        lock = json.loads((self.root / "package-lock.json").read_text())
        self.assertEqual((lock["version"], lock["packages"][""]["version"]), ("0.5.2", "0.5.2"))
        self.assertIn(" * Version: 0.5.2\n", (self.root / "api-volunteer-manager.php").read_text())

    def test_rejects_mismatched_or_invalid_versions_without_writing(self):
        plugin = self.root / "api-volunteer-manager.php"
        plugin.write_text("<?php\n/**\n * Version: 1.0.0\n */\n")
        original = (self.root / "package.json").read_text()
        with self.assertRaisesRegex(ValueError, "versions differ"):
            set_release_version.update(self.root, "0.5.2")
        self.assertEqual((self.root / "package.json").read_text(), original)
        with self.assertRaisesRegex(ValueError, "Invalid release version"):
            set_release_version.update(self.root, "v0.5.2")


if __name__ == "__main__":
    unittest.main()
