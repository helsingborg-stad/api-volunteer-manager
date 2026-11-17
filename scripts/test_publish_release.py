import json
import os
import shutil
import subprocess
import tempfile
import unittest
import zipfile
from pathlib import Path

import release_package


class PublishReleaseTest(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.base = Path(self.temp.name).resolve()
        self.root = self.base / "repo"
        self.root.mkdir()
        self.remote = self.base / "remote.git"
        self.run_git("init", "--bare", "-q", str(self.remote), cwd=self.base)
        self.run_git("init", "-q", "-b", "master")
        self.run_git("config", "user.name", "Release test")
        self.run_git("config", "user.email", "release-test@example.test")
        (self.root / "marker").write_text("first")
        self.run_git("add", "marker")
        self.run_git("commit", "-q", "-m", "first", "-m", "Co-authored-by: Copilot App <223556219+Copilot@users.noreply.github.com>")
        self.old = self.git_output("rev-parse", "HEAD")
        (self.root / "marker").write_text("second")
        self.run_git("commit", "-q", "-am", "second", "-m", "Co-authored-by: Copilot App <223556219+Copilot@users.noreply.github.com>")
        self.sha = self.git_output("rev-parse", "HEAD")
        self.run_git("remote", "add", "origin", str(self.remote))
        self.run_git("push", "-q", "origin", "master")
        scripts = self.root / "scripts"
        scripts.mkdir()
        for name in ("publish_release.sh", "release_package.py"):
            shutil.copy(Path(__file__).parent / name, scripts / name)
        self.archive = self.base / "release.zip"
        with zipfile.ZipFile(self.archive, "w") as archive:
            for name in release_package.REQUIRED:
                content = b"content"
                if name == "dist/manifest.json":
                    content = json.dumps({
                        "css/api-volunteer-manager.css": "css/app.css",
                        "js/api-volunteer-manager.js": "js/app.js",
                    }).encode()
                archive.writestr(name, content)
            archive.writestr("dist/css/app.css", "css")
            archive.writestr("dist/js/app.js", "js")
        mock = self.base / "bin"
        mock.mkdir()
        gh = mock / "gh"
        gh.write_text("""#!/usr/bin/env python3
import json, os, shutil, sys
args = sys.argv[1:]
with open(os.environ['MOCK_LOG'], 'a') as log:
    log.write(' '.join(args) + '\\n')
mode = os.environ['MOCK_MODE']
if args[0] == 'api':
    if mode == 'error':
        print('HTTP 503', file=sys.stderr); sys.exit(1)
    if mode == 'new':
        print('HTTP 404', file=sys.stderr); sys.exit(1)
    print(json.dumps({'draft': mode == 'draft', 'assets': [] if mode in ('missing', 'draft') else [{'name': 'api-volunteer-manager-0.5.2.zip'}]}))
elif args[:2] == ['release', 'download']:
    target = os.path.join(args[args.index('--dir') + 1], 'api-volunteer-manager-0.5.2.zip')
    if mode == 'different':
        open(target, 'w').write('different')
    else:
        shutil.copy(os.environ['MOCK_ARCHIVE'], target)
""")
        gh.chmod(0o755)
        self.env = os.environ.copy()
        self.env.update(PATH=str(mock) + os.pathsep + self.env["PATH"],
                        MOCK_LOG=str(self.base / "calls"), MOCK_ARCHIVE=str(self.archive))

    def tearDown(self):
        self.temp.cleanup()

    def run_git(self, *args, cwd=None):
        subprocess.run(["git", *args], cwd=cwd or self.root, check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)

    def git_output(self, *args):
        return subprocess.check_output(["git", *args], cwd=self.root, text=True).strip()

    def publish(self, mode):
        self.env["MOCK_MODE"] = mode
        return subprocess.run(
            ["bash", "scripts/publish_release.sh", "0.5.2", self.sha, str(self.archive), "owner/repo"],
            cwd=self.root, env=self.env, text=True, capture_output=True,
        )

    def tag(self, sha):
        self.run_git("tag", "0.5.2", sha)
        self.run_git("push", "-q", "origin", "refs/tags/0.5.2")

    def calls(self):
        path = self.base / "calls"
        return path.read_text() if path.exists() else ""

    def test_api_error_does_not_create_tag(self):
        result = self.publish("error")
        self.assertNotEqual(result.returncode, 0)
        self.assertIn("HTTP 503", result.stderr)
        self.assertEqual(self.git_output("ls-remote", "origin", "refs/tags/0.5.2"), "")

    def test_existing_tag_must_match_source(self):
        self.tag(self.old)
        result = self.publish("missing")
        self.assertNotEqual(result.returncode, 0)
        self.assertIn("different source commit", result.stderr)
        self.assertNotIn("release upload", self.calls())

    def test_published_release_missing_asset_is_not_modified(self):
        self.tag(self.sha)
        result = self.publish("missing")
        self.assertNotEqual(result.returncode, 0)
        self.assertIn("Published release is missing", result.stderr)
        self.assertNotIn("release upload", self.calls())

    def test_conflicting_asset_is_not_replaced(self):
        self.tag(self.sha)
        result = self.publish("different")
        self.assertNotEqual(result.returncode, 0)
        self.assertIn("differs", result.stderr)
        self.assertNotIn("release upload", self.calls())
        self.assertNotIn("release edit", self.calls())

    def test_existing_release_without_tag_is_not_modified(self):
        result = self.publish("missing")
        self.assertNotEqual(result.returncode, 0)
        self.assertIn("Existing release has no tag", result.stderr)
        self.assertEqual(self.git_output("ls-remote", "origin", "refs/tags/0.5.2"), "")

    def test_draft_can_resume_after_upload_failure(self):
        self.tag(self.sha)
        result = self.publish("draft")
        self.assertEqual(result.returncode, 0, result.stderr)
        calls = self.calls()
        self.assertLess(calls.index("release upload"), calls.index("release download"))
        self.assertLess(calls.index("release download"), calls.index("release edit"))

    def test_published_matching_asset_is_not_modified(self):
        self.tag(self.sha)
        result = self.publish("same")
        self.assertEqual(result.returncode, 0, result.stderr)
        self.assertNotIn("release upload", self.calls())
        self.assertNotIn("release edit", self.calls())

    def test_new_release_is_published_after_verification(self):
        result = self.publish("new")
        self.assertEqual(result.returncode, 0, result.stderr)
        calls = self.calls()
        self.assertLess(calls.index("release create"), calls.index("release download"))
        self.assertLess(calls.index("release download"), calls.index("release edit"))
        self.assertIn(self.sha, self.git_output("ls-remote", "origin", "refs/tags/0.5.2"))


if __name__ == "__main__":
    unittest.main()
