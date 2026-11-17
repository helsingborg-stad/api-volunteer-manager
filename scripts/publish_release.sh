#!/usr/bin/env bash
set -euo pipefail

version=$1
source_sha=$2
archive=$3
repository=$4
asset="api-volunteer-manager-$version.zip"
tag_ref="refs/tags/$version"

[[ "$version" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] || { echo "Invalid version" >&2; exit 1; }
[[ "$source_sha" =~ ^[0-9a-f]{40}$ ]] || { echo "Invalid source SHA" >&2; exit 1; }
[[ "$repository" =~ ^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$ ]] || { echo "Invalid repository" >&2; exit 1; }
[[ $(git rev-parse HEAD) == "$source_sha" ]] || { echo "Wrong source checkout" >&2; exit 1; }
[[ $(git ls-remote origin refs/heads/master | cut -f1) == "$source_sha" ]] || {
    echo "master advanced during the build; retry from its current tip" >&2; exit 1;
}
python3 scripts/release_package.py check "$archive"
expected=$(sha256sum "$archive" | cut -d' ' -f1)
scratch=$(mktemp -d)
trap 'rm -rf "$scratch"' EXIT

release_exists=false
if gh api "repos/$repository/releases/tags/$version" >"$scratch/release.json" 2>"$scratch/error"; then
    release_exists=true
elif ! grep -q 'HTTP 404' "$scratch/error"; then
    cat "$scratch/error" >&2
    exit 1
fi

remote_tag=$(git ls-remote origin "$tag_ref" | cut -f1)
if [[ -n "$remote_tag" ]]; then
    git fetch origin "$tag_ref"
    [[ $(git rev-parse 'FETCH_HEAD^{}') == "$source_sha" ]] || {
        echo "Existing tag points at a different source commit" >&2; exit 1;
    }
else
    [[ "$release_exists" == false ]] || {
        echo "Existing release has no tag; manual intervention required" >&2; exit 1;
    }
    git push origin "$source_sha:$tag_ref"
fi

if [[ "$release_exists" == true ]]; then
    existing=$(jq -r --arg asset "$asset" '[.assets[] | select(.name == $asset)] | length' "$scratch/release.json")
    if [[ "$existing" -gt 0 ]]; then
        gh release download "$version" --repo "$repository" --pattern "$asset" --dir "$scratch"
    else
        [[ $(jq -r .draft "$scratch/release.json") == true ]] || {
            echo "Published release is missing the asset; manual intervention required" >&2; exit 1;
        }
        gh release upload "$version" "$archive#$asset" --repo "$repository"
    fi
else
    gh release create "$version" "$archive#$asset" --repo "$repository" \
        --verify-tag --draft --generate-notes
fi

git fetch origin "$tag_ref"
[[ $(git rev-parse 'FETCH_HEAD^{}') == "$source_sha" ]] || {
    echo "Release tag points at a different source commit" >&2; exit 1;
}
if [[ ! -f "$scratch/$asset" ]]; then
    gh release download "$version" --repo "$repository" --pattern "$asset" --dir "$scratch"
fi
[[ $(sha256sum "$scratch/$asset" | cut -d' ' -f1) == "$expected" ]] || {
    echo "Release asset differs from the validated build" >&2; exit 1;
}
if [[ "$release_exists" != true || $(jq -r .draft "$scratch/release.json") == true ]]; then
    gh release edit "$version" --repo "$repository" --draft=false
fi
