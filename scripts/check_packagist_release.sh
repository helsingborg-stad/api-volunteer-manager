#!/usr/bin/env bash
set -euo pipefail

version=$1
[[ "$version" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] || { echo "Invalid version" >&2; exit 1; }
expected='https://github.com/helsingborg-stad/api-volunteer-manager/releases/download/%prettyVersion%/api-volunteer-manager-%prettyVersion%.zip'
curl --fail --silent --show-error https://repo.packagist.org/p2/helsingborg-stad/api-volunteer-manager.json |
    jq -e --arg version "$version" --arg url "$expected" \
        '.packages["helsingborg-stad/api-volunteer-manager"] | any(.version == $version and .dist.type == "zip" and .dist.url == $url)' >/dev/null
echo "Packagist exposes the release asset URL for $version."
