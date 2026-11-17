#!/usr/bin/env python3
"""Update only the release version fields."""

import json
import re
import sys
from pathlib import Path


def update(root, version):
    if not re.fullmatch(r"\d+\.\d+\.\d+", version):
        raise ValueError(f"Invalid release version: {version}")
    package = root / "package.json"
    lock = root / "package-lock.json"
    plugin = root / "api-volunteer-manager.php"
    package_text = package.read_text()
    lock_text = lock.read_text()
    plugin_text = plugin.read_text()
    current = json.loads(package_text)["version"]
    lock_data = json.loads(lock_text)
    if lock_data["version"] != current or lock_data["packages"][""]["version"] != current:
        raise ValueError("Package and lockfile versions differ")
    header = re.compile(r"(?m)^(\s*\* Version:\s+)(\d+\.\d+\.\d+)(\s*)$")
    match = header.search(plugin_text)
    if not match or match.group(2) != current:
        raise ValueError("Plugin header and package versions differ")
    old = f'"version": "{current}"'
    new = f'"version": "{version}"'
    if package_text.count(old) != 1 or lock_text.count(old) < 2:
        raise ValueError("Could not locate package version fields")
    package.write_text(package_text.replace(old, new, 1))
    lock.write_text(lock_text.replace(old, new, 2))
    plugin.write_text(header.sub(lambda m: m.group(1) + version + m.group(3), plugin_text, count=1))


if __name__ == "__main__":
    try:
        update(Path.cwd(), sys.argv[1])
    except (ValueError, KeyError, IndexError, OSError) as error:
        sys.exit(f"Could not update release version: {error}")
