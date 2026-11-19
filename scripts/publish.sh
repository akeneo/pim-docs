#!/usr/bin/env bash
# Publishes ./public (or $OUTPUT_DIR) to the site bucket, then invalidates the CDN cache.
#
# Required environment:
#   SITE_BUCKET   e.g. akecld-prd-pim-docs-prd-site
# Optional environment:
#   GCP_PROJECT   e.g. akecld-prd-pim-docs-prd (needed to invalidate the CDN cache)
#   SITE_URL_MAP  e.g. pim-docs-site (CDN invalidation is skipped when empty)
#   DRY_RUN=1     only show what would change
#
# gcloud must be authenticated with an identity allowed to write in the bucket
# (in CI: the main service account of the project, impersonated through OIDC).
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUTPUT_DIR="${OUTPUT_DIR:-${ROOT_DIR}/public}"
: "${SITE_BUCKET:?SITE_BUCKET must be set}"

if [[ ! -f "${OUTPUT_DIR}/versions.json" ]]; then
    echo "${OUTPUT_DIR} does not look like a complete build (no versions.json). Run scripts/build.sh first." >&2
    exit 1
fi

DRY_RUN_FLAG=()
if [[ "${DRY_RUN:-0}" == "1" ]]; then
    DRY_RUN_FLAG=(--dry-run)
fi

# One hour of cache for every object; the CDN cache is invalidated right after the synchronization.
echo "==> Synchronizing ${OUTPUT_DIR} to gs://${SITE_BUCKET}"
gcloud storage rsync "${OUTPUT_DIR}" "gs://${SITE_BUCKET}" \
    --recursive \
    --delete-unmatched-destination-objects \
    --checksums-only \
    --cache-control="public, max-age=3600" \
    "${DRY_RUN_FLAG[@]}"

if [[ "${DRY_RUN:-0}" == "1" ]]; then
    exit 0
fi

if [[ -n "${SITE_URL_MAP:-}" ]]; then
    : "${GCP_PROJECT:?GCP_PROJECT must be set to invalidate the CDN cache}"
    echo "==> Invalidating the CDN cache of ${SITE_URL_MAP}"
    gcloud compute url-maps invalidate-cdn-cache "${SITE_URL_MAP}" \
        --project "${GCP_PROJECT}" \
        --path "/*" \
        --global \
        --async
fi
