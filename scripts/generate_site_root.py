#!/usr/bin/env python3
"""Generates the files served at the root of the documentation site.

- versions.json: read by the version menu of every version (/_static/js/versions.js)
- index.html:    list of the published versions (the load balancer redirects "/" to
                 /latest/index.html, this page is used when browsing a local build)
- 404.html, robots.txt and favicon.ico: copied from site/
"""
import argparse
import html
import json
import shutil
from pathlib import Path


def version_label(version: str) -> str:
    return f"v{version}"


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--site-dir", type=Path, required=True)
    parser.add_argument("--output-dir", type=Path, required=True)
    parser.add_argument("--latest", required=True)
    parser.add_argument("versions", nargs="+")
    args = parser.parse_args()

    args.output_dir.mkdir(parents=True, exist_ok=True)

    versions = [
        {"label": version_label(v), "url": f"/{v}/index.html"} for v in args.versions
    ]
    (args.output_dir / "versions.json").write_text(
        json.dumps(versions, indent=4) + "\n", encoding="utf-8"
    )

    items = "\n".join(
        f'        <li><a href="/{html.escape(v)}/index.html">{html.escape(version_label(v))}</a>'
        + (" (latest)" if v == args.latest else "")
        + "</li>"
        for v in args.versions
    )
    template = (args.site_dir / "index.html.tpl").read_text(encoding="utf-8")
    (args.output_dir / "index.html").write_text(
        template.replace("{{ versions }}", items), encoding="utf-8"
    )

    for name in ("404.html", "robots.txt", "favicon.ico"):
        shutil.copyfile(args.site_dir / name, args.output_dir / name)


if __name__ == "__main__":
    main()
