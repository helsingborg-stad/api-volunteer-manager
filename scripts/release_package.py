#!/usr/bin/env python3
"""Create and validate the Composer distribution archive."""

import argparse
import json
import re
import stat
import subprocess
import zipfile
from pathlib import Path, PurePosixPath

ROOT_FILES = {"api-volunteer-manager.php", "Public.php", "composer.json", "composer.lock", "LICENSE", "README.md"}
ROOT_DIRS = {"assets", "languages"}
REQUIRED = ROOT_FILES | {
    "source/php/App.php",
    "source/php/AcfFields/json/assignment.json",
    "source/php/AcfFields/php/assignment.php",
    "dist/manifest.json",
}
ENTRY_KEYS = {"css/api-volunteer-manager.css", "js/api-volunteer-manager.js"}
FORBIDDEN = re.compile(r"(^|/)(vendor|node_modules|\.git|\.github|\.env(?:\..*)?|\.npmrc|.*\.(?:pem|key|crt))(/|$)", re.I)


def allowed(name):
    path = PurePosixPath(name)
    return (
        "\\" not in name
        and not path.is_absolute()
        and all(part not in ("", ".", "..") for part in name.split("/"))
        and not FORBIDDEN.search(name)
        and (
            name in ROOT_FILES
            or (len(path.parts) > 1 and path.parts[0] in ROOT_DIRS | {"dist"})
            or (len(path.parts) > 2 and path.parts[:2] == ("source", "php"))
        )
    )


def validate(archive):
    with zipfile.ZipFile(archive) as package:
        names = package.namelist()
        files = set(names)
        if len(files) != len(names) or not all(allowed(name) for name in names):
            raise ValueError("Duplicate, forbidden or unrooted archive entry")
        if not REQUIRED <= files:
            raise ValueError(f"Missing required files: {sorted(REQUIRED - files)}")
        for info in package.infolist():
            mode = info.external_attr >> 16
            if info.is_dir() or stat.S_ISLNK(mode) or (stat.S_IFMT(mode) not in (0, stat.S_IFREG)):
                raise ValueError(f"Non-regular archive entry: {info.filename}")
        manifest = json.loads(package.read("dist/manifest.json"))
        if not isinstance(manifest, dict) or not ENTRY_KEYS <= manifest.keys():
            raise ValueError("Missing required manifest entries")
        for entry in manifest.values():
            if not isinstance(entry, str) or not allowed("dist/" + entry) or "dist/" + entry not in files:
                raise ValueError(f"Unsafe or missing manifest asset: {entry}")
        corrupt = package.testzip()
        if corrupt:
            raise ValueError(f"Corrupt archive member: {corrupt}")


def create(root, archive):
    root = root.resolve()
    tracked = subprocess.check_output(
        ["git", "-C", str(root), "ls-files", "-z", "--", *sorted(ROOT_FILES | ROOT_DIRS), "source/php"]
    ).decode().split("\0")
    names = {name for name in tracked if name and allowed(name)}
    dist = root / "dist"
    if not dist.is_dir() or dist.is_symlink():
        raise ValueError("Missing or unsafe dist directory")
    for path in dist.rglob("*"):
        if path.is_symlink():
            raise ValueError(f"Symlink in dist: {path}")
        if path.is_file():
            names.add(path.relative_to(root).as_posix())
    if not REQUIRED <= names:
        raise ValueError(f"Missing build inputs: {sorted(REQUIRED - names)}")
    archive.parent.mkdir(parents=True, exist_ok=True)
    with zipfile.ZipFile(archive, "w", compression=zipfile.ZIP_DEFLATED) as package:
        for name in sorted(names):
            path = root / name
            if (
                not allowed(name)
                or not path.is_file()
                or not path.resolve().is_relative_to(root)
                or any(parent.is_symlink() for parent in (path, *path.parents) if parent != root and parent.is_relative_to(root))
            ):
                raise ValueError(f"Unsafe or missing input: {name}")
            info = zipfile.ZipInfo(name, date_time=(1980, 1, 1, 0, 0, 0))
            info.external_attr = 0o100644 << 16
            info.compress_type = zipfile.ZIP_DEFLATED
            package.writestr(info, path.read_bytes())
    validate(archive)


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("command", choices=("create", "check"))
    parser.add_argument("archive", type=Path)
    parser.add_argument("--root", type=Path, default=Path(__file__).resolve().parents[1])
    args = parser.parse_args()
    try:
        if args.command == "create":
            create(args.root.resolve(), args.archive)
        else:
            validate(args.archive)
    except (ValueError, OSError, subprocess.CalledProcessError, zipfile.BadZipFile) as error:
        parser.exit(1, f"Release archive invalid: {error}\n")


if __name__ == "__main__":
    main()
