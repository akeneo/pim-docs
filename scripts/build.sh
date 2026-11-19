#!/usr/bin/env bash
# Builds the static documentation site into ./public (or $OUTPUT_DIR).
#
#   scripts/build.sh            build every version listed in build/versions.txt
#   scripts/build.sh 7.0 3.2    build only these versions (plus the site root files)
#
# Each version is built from versions/<version>/ with the toolchain named in
# build/versions.txt (a target of build/Dockerfile). Only Docker is required.
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUTPUT_DIR="${OUTPUT_DIR:-${ROOT_DIR}/public}"
IMAGE="${IMAGE:-pim-docs-archive-builder}"

mapfile -t MANIFEST < <(grep -vE '^\s*(#|$)' "${ROOT_DIR}/build/versions.txt")
ALL_VERSIONS=()
declare -A TOOLCHAIN
for line in "${MANIFEST[@]}"; do
    read -r version toolchain <<<"${line}"
    ALL_VERSIONS+=("${version}")
    TOOLCHAIN["${version}"]="${toolchain}"
done
LATEST="${ALL_VERSIONS[0]}"

if [[ $# -gt 0 ]]; then
    REQUESTED=("$@")
else
    REQUESTED=("${ALL_VERSIONS[@]}")
fi

for version in "${REQUESTED[@]}"; do
    if [[ -z "${TOOLCHAIN[${version}]:-}" ]]; then
        echo "Unknown version '${version}': it is not listed in build/versions.txt" >&2
        exit 1
    fi
    if [[ ! -f "${ROOT_DIR}/versions/${version}/conf.py" ]]; then
        echo "versions/${version}/conf.py not found" >&2
        exit 1
    fi
done

for toolchain in $(printf '%s\n' "${REQUESTED[@]}" | while read -r v; do echo "${TOOLCHAIN[${v}]}"; done | sort -u); do
    echo "==> Building the ${toolchain} toolchain image"
    docker build --quiet --target "${toolchain}" --tag "${IMAGE}:${toolchain}" "${ROOT_DIR}/build" >/dev/null
done

mkdir -p "${OUTPUT_DIR}"

# Space-separated list read by the theme of each version to render the version menu
# when /versions.json cannot be fetched (e.g. when a version is opened from the file system).
PIM_VERSIONS="${ALL_VERSIONS[*]}"

for version in "${REQUESTED[@]}"; do
    toolchain="${TOOLCHAIN[${version}]}"
    echo "==> Building ${version} with the ${toolchain} toolchain"
    rm -rf "${OUTPUT_DIR:?}/${version}"
    mkdir -p "${OUTPUT_DIR}/${version}"
    docker run --rm \
        --user "$(id -u):$(id -g)" \
        --volume "${ROOT_DIR}/versions/${version}:/docs:ro" \
        --volume "${OUTPUT_DIR}/${version}:/out" \
        --env PIM_VERSION="${version}" \
        --env PIM_VERSIONS="${PIM_VERSIONS}" \
        "${IMAGE}:${toolchain}" \
        sphinx-build -q -b html -d /tmp/doctrees /docs /out
    # Build metadata, not meant to be published
    rm -f "${OUTPUT_DIR}/${version}/.buildinfo"
done

if printf '%s\n' "${REQUESTED[@]}" | grep -qxF "${LATEST}"; then
    echo "==> Publishing ${LATEST} as latest"
    rm -rf "${OUTPUT_DIR:?}/latest"
    cp -R "${OUTPUT_DIR}/${LATEST}" "${OUTPUT_DIR}/latest"
fi

echo "==> Generating the site root files"
python3 "${ROOT_DIR}/scripts/generate_site_root.py" \
    --site-dir "${ROOT_DIR}/site" \
    --output-dir "${OUTPUT_DIR}" \
    --latest "${LATEST}" \
    "${ALL_VERSIONS[@]}"

echo "Done: ${OUTPUT_DIR}"
