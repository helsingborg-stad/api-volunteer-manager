#!/usr/bin/env bash
set -euo pipefail

archive=$(realpath "$1")
version=$2
root=$(realpath "${3:-.}")
[[ "$version" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] || { echo "Invalid version" >&2; exit 1; }
scratch=$(mktemp -d)
trap 'rm -rf "$scratch"' EXIT
cp "$archive" "$scratch/api-volunteer-manager-$version.zip"

for mode in dist source; do
    mkdir "$scratch/$mode"
    cd "$scratch/$mode"
    composer init --no-interaction --name=volunteer-manager/release-test --no-ansi
    package=$(unzip -p "$archive" composer.json | jq --arg version "$version" --arg archive "$scratch/api-volunteer-manager-%prettyVersion%.zip" \
        '. + {version: $version, dist: {type: "zip", url: $archive}} | del(."require-dev", .scripts)')
    if [[ "$mode" == source ]]; then
        sha=$(git -C "$root" rev-parse HEAD)
        package=$(jq --arg root "$root" --arg sha "$sha" \
            '. + {source: {type: "git", url: $root, reference: $sha}} | del(.dist)' <<< "$package")
    fi
    jq --argjson package "$package" '.repositories = [{type: "package", package: $package}]' \
        composer.json > composer.tmp.json
    mv composer.tmp.json composer.json
    composer require "helsingborg-stad/api-volunteer-manager:$version" "--prefer-$mode" \
        --no-interaction --no-scripts --no-plugins --no-progress
    plugin=vendor/helsingborg-stad/api-volunteer-manager
    test -f "$plugin/dist/manifest.json"
    test -f "$plugin/source/php/AcfFields/json/assignment.json"
    test ! -d "$plugin/vendor"
    test ! -d "$plugin/node_modules"
    php "$root/scripts/test_bootstrap.php" "$plugin/api-volunteer-manager.php" vendor/autoload.php
    if [[ "$mode" == dist ]]; then
        if php "$root/scripts/test_bootstrap.php" "$plugin/api-volunteer-manager.php" >"$scratch/missing.log" 2>&1; then
            echo "Plugin unexpectedly started without a Composer autoloader" >&2
            exit 1
        fi
        grep -q 'requires its Composer dependencies and autoloader' "$scratch/missing.log"
    fi
done

php "$root/scripts/test_bootstrap.php" "$root/api-volunteer-manager.php"
