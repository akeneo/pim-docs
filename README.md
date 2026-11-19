# Akeneo PIM documentation: On-Premise and Flexibility archive

This branch (`flexibility-docs-archive`) gathers the technical documentation of every Akeneo PIM
version deployed On-Premise or on Flexibility (PaaS), from 1.0 to 7.0, one directory per version.
The CI builds all of them into a single static site, published on docs.akeneo.com.

The `master` branch documents Akeneo PIM SaaS (Serenity) and is not part of this archive.

## Layout

```
versions/<version>/   sources of each version, imported from the <version> branch (history kept)
build/versions.txt    published versions, newest first, and the toolchain building each of them
build/Dockerfile      the toolchains (Docker targets "modern" and "legacy")
scripts/build.sh      builds the site into public/
scripts/publish.sh    publishes public/ to the GCS bucket and invalidates the CDN cache
site/                 files served at the root of the site (404 page, robots.txt, list of versions)
helpjuice/            how to bring the changes made on Helpjuice back into Git
.circleci/config.yml  build on every push, publication from flexibility-docs-archive
```

Each `versions/<version>/` directory was imported with a subtree merge of the `<version>` branch,
so the history of every file is still there:

```bash
git log -- versions/7.0/install_pim/index.rst      # changes made in this branch
git log origin/7.0 -- install_pim/index.rst        # history before the import
```

## Build the site

Only Docker and Python 3 are required.

```bash
make build                          # every version, into public/ (about 2 minutes)
make build-version VERSIONS="7.0"   # some versions only
make serve                          # http://localhost:8000/latest/index.html
```

The site is a directory tree of static files that any static hosting can serve:

```
public/
├── 1.0/ … 7.0/      one Sphinx build per version
├── latest/          copy of the newest version (first line of build/versions.txt)
├── versions.json    read by the version menu of every page
├── index.html       list of the versions
├── 404.html
└── robots.txt       the archive is not indexed, as on the former server
```

### Toolchains

| Versions | Toolchain | Why |
|---|---|---|
| 4.0 to 7.0 | `modern`: Python 3.12, Sphinx 7.4 | plain Sphinx, as on their branches |
| 1.0 to 3.2 | `legacy`: Python 3.7, Sphinx 1.8, sphinx-php 1.0.10, sphinxcontrib.youtube | the `sensio.sphinx.*` extensions do not load on recent Sphinx releases |

Every dependency is pinned, so the archive keeps building the same way over time.

### Known gaps compared to the former server

- The PIM UI styleguide (`1.7/styleguide/`, and `design_pim/styleguide/` from 2.0 to 4.0) is a
  PHP/Twig page that needs a `composer install` of the matching PIM version; the former server
  rendered it with PHP. The Sphinx build only produces its placeholder page ("This file is
  generated to be redirected to styleguide/index.php").
- The theme still loads Algolia DocSearch and Google Analytics (Universal Analytics, which no
  longer collects data) from their CDNs, as on the former server.

## Publish

The CI publishes the site on every push to `flexibility-docs-archive`
([CircleCI config](.circleci/config.yml)):

1. `build`: `scripts/build.sh`, the result is kept as an artifact (`pim-docs-site.tar.gz`),
2. `publish`: `scripts/publish.sh`, which synchronizes `public/` to
   `gs://akecld-prd-pim-docs-prd-site` and invalidates the Cloud CDN cache.

The GCP project `akecld-prd-pim-docs-prd` and its hosting (bucket, HTTPS load balancer with a
Google-managed certificate and Cloud CDN) are defined in
[akeneo/gcp-projects](https://github.com/akeneo/gcp-projects), in
`product/documentation/pim-docs`. The CI authenticates through OIDC (CircleCI context `PIM_DOCS`),
without any service account key.

To publish by hand, with an identity allowed to write in the bucket:

```bash
SITE_BUCKET=akecld-prd-pim-docs-prd-site \
GCP_PROJECT=akecld-prd-pim-docs-prd \
SITE_URL_MAP=pim-docs-site \
make publish
```

## Change the documentation

Edit the sources in `versions/<version>/` and open a pull request on `flexibility-docs-archive`.
The former version branches (`1.0` to `7.0`) are not updated anymore.

To add or remove a published version, edit `build/versions.txt`.
