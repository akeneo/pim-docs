#!/usr/bin/env python3
"""Extracts the Helpjuice articles of a PIM version and finds what changed since the Git sources.

Helpjuice (help.akeneo.com) became the source of truth of the help center in 2023. This script
fetches the published articles of one version from the public site, without any API key, saves
them, matches each one with a Markdown source file of a Git checkout, and reports the articles
whose text differs from that source, so the changes can be brought back into Git.

    python3 helpjuice/extract.py --version v7 --out /tmp/hj-v7 \\
        --sources ../pim-helpcenter/content/md     # checkout of the v7 branch

Output (in --out):
    articles/<category>/<slug>.html   body of each article, as published
    articles.json                     url, Helpjuice id, title, category, matched source, similarity
    report.md                         articles changed on Helpjuice, missing in Git, or only in Git
    diffs/<category>/<slug>.diff      text diff (Git source vs Helpjuice) of each changed article,
                                      lower-cased: use the saved HTML body for the exact wording

Only the Python standard library is used. Run it with `python3 -I`.
"""
import argparse
import difflib
import html
import json
import re
import sys
import time
import urllib.request
from html.parser import HTMLParser
from pathlib import Path
from xml.etree import ElementTree

DEFAULT_SITE = "https://help.akeneo.com"
USER_AGENT = "pim-docs-helpjuice-extract/1.0"
SITEMAP_NS = {"sm": "http://www.sitemaps.org/schemas/sitemap/0.9"}


def fetch(url: str, retries: int = 3) -> str:
    for attempt in range(retries):
        try:
            request = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
            with urllib.request.urlopen(request, timeout=30) as response:
                return response.read().decode("utf-8", errors="replace")
        except Exception:  # noqa: BLE001 (network errors are retried, then raised)
            if attempt == retries - 1:
                raise
            time.sleep(2 ** attempt)
    raise AssertionError("unreachable")


def article_urls(site: str, version: str) -> list[str]:
    """Article URLs of a version: /en_US/<version>/<slug> and /en_US/<version>-<category>/<slug>."""
    root = ElementTree.fromstring(fetch(f"{site}/sitemap.xml"))
    pattern = re.compile(rf"^{re.escape(site)}/en_US/{re.escape(version)}(-[^/]+)?/[^/]+$")
    urls = [loc.text.strip() for loc in root.findall(".//sm:loc", SITEMAP_NS) if loc.text]
    return sorted(u for u in urls if pattern.match(u))


class ArticleParser(HTMLParser):
    """Extracts the title, the Helpjuice id and the body HTML of an article page."""

    def __init__(self) -> None:
        super().__init__(convert_charrefs=False)
        self.title = ""
        self.article_id = ""
        self._in_title = False
        self._body_depth = 0
        self._body: list[str] = []

    def handle_starttag(self, tag, attrs):
        attributes = dict(attrs)
        classes = (attributes.get("class") or "").split()
        if not self.article_id and (attributes.get("id") or "").startswith("article__"):
            self.article_id = attributes["id"].removeprefix("article__")
        if tag == "h1" and attributes.get("data-helpjuice-element") == "Article Title":
            self._in_title = True
        if self._body_depth:
            self._body.append(self.get_starttag_text())
            if tag not in VOID_TAGS:
                self._body_depth += 1
        elif tag == "div" and "helpjuice-article-body-content" in classes:
            self._body_depth = 1

    def handle_startendtag(self, tag, attrs):
        if self._body_depth:
            self._body.append(self.get_starttag_text())

    def handle_endtag(self, tag):
        if tag == "h1":
            self._in_title = False
        if self._body_depth and tag not in VOID_TAGS:
            self._body_depth -= 1
            if self._body_depth:
                self._body.append(f"</{tag}>")

    def handle_data(self, data):
        if self._in_title:
            self.title += data
        if self._body_depth:
            self._body.append(data)

    def handle_entityref(self, name):
        self.handle_data(f"&{name};")

    def handle_charref(self, name):
        self.handle_data(f"&#{name};")

    @property
    def body(self) -> str:
        return "".join(self._body).strip()


VOID_TAGS = {"area", "br", "col", "embed", "hr", "img", "input", "link", "meta", "source", "track", "wbr"}
BLOCK_TAGS = {"p", "div", "li", "tr", "h1", "h2", "h3", "h4", "h5", "h6", "pre", "blockquote", "table", "ul", "ol", "br"}


class TextExtractor(HTMLParser):
    def __init__(self) -> None:
        super().__init__()
        self.parts: list[str] = []

    def handle_starttag(self, tag, attrs):
        if tag in BLOCK_TAGS:
            self.parts.append("\n")

    def handle_endtag(self, tag):
        if tag in BLOCK_TAGS:
            self.parts.append("\n")

    def handle_data(self, data):
        self.parts.append(data)


def html_to_lines(body: str) -> list[str]:
    extractor = TextExtractor()
    extractor.feed(body)
    return normalize_lines("".join(extractor.parts))


def markdown_to_lines(markdown: str) -> list[str]:
    markdown = re.sub(r"^---\n.*?\n---\n", "", markdown, flags=re.S)  # front matter
    markdown = re.sub(r"<[^>]+>", "\n", markdown)  # inline HTML
    markdown = re.sub(r"!\[[^\]]*\]\([^)]*\)", "", markdown)  # images
    markdown = re.sub(r"\[([^\]]*)\]\([^)]*\)", r"\1", markdown)  # links: keep the text
    markdown = re.sub(r"^\s{0,3}(#{1,6}|[-*+]|\d+\.|>)\s+", "", markdown, flags=re.M)
    markdown = re.sub(r"^\|?[-:| ]+\|?$", "", markdown, flags=re.M)  # table separators
    markdown = re.sub(r"[*`]|(?<!\w)_+|_+(?!\w)", "", markdown)  # emphasis, keeping en_US intact
    markdown = markdown.replace("|", " ")
    return normalize_lines(html.unescape(markdown))


EMOJI = re.compile(r":[a-z0-9_+-]+:|[\U0001F000-\U0001FAFF\u2600-\u27BF\uFE0F]|(?<!\w)[;:]-?[)(DPp](?!\w)")


def normalize_lines(text: str) -> list[str]:
    """Sentences of the text, lower-cased, one per line.

    Comparing sentences instead of lines makes the diff insensitive to the differences the
    migration to Helpjuice introduced without changing the content: paragraphs re-wrapped,
    headings upper-cased, ":::info" blocks turned into callouts, emoji shortcodes rendered.
    """
    kept = []
    for line in text.splitlines():
        line = line.strip()
        if line and not line.startswith(":::"):
            kept.append(line)
    text = EMOJI.sub("", " ".join(kept).replace("\N{NO-BREAK SPACE}", " "))
    text = re.sub(r"\s+", " ", text).strip().casefold()
    return [sentence for sentence in re.split(r"(?<=[.!?])\s+", text) if sentence]


def normalize_title(title: str) -> str:
    title = re.sub(r"[*_`]", "", html.unescape(title)).lower()
    return re.sub(r"[^a-z0-9]+", " ", title).strip()


def load_sources(sources: Path) -> dict[str, Path]:
    """Markdown sources indexed by normalized front matter title."""
    index: dict[str, Path] = {}
    for path in sorted(sources.rglob("*.md")):
        match = re.search(r"^title:\s*(.+)$", path.read_text(encoding="utf-8", errors="replace"), re.M)
        if match:
            index.setdefault(normalize_title(match.group(1).strip().strip("'\"")), path)
    return index


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("--version", required=True, help="Helpjuice version prefix, e.g. v7, v6, v1-7")
    parser.add_argument("--out", type=Path, required=True)
    parser.add_argument("--sources", type=Path, help="Directory of the Markdown sources of the same version")
    parser.add_argument("--site", default=DEFAULT_SITE)
    parser.add_argument("--delay", type=float, default=0.5, help="Seconds between two requests")
    args = parser.parse_args()

    urls = article_urls(args.site, args.version)
    if not urls:
        print(f"No article found for '{args.version}' in {args.site}/sitemap.xml", file=sys.stderr)
        return 1
    print(f"{len(urls)} articles found for {args.version}", file=sys.stderr)

    sources = load_sources(args.sources) if args.sources else {}
    matched_sources: set[Path] = set()
    records = []

    for number, url in enumerate(urls, start=1):
        category, slug = url.rsplit("/", 2)[-2:]
        page = ArticleParser()
        page.feed(fetch(url))
        title = html.unescape(page.title).strip()

        article_file = args.out / "articles" / category / f"{slug}.html"
        article_file.parent.mkdir(parents=True, exist_ok=True)
        article_file.write_text(page.body + "\n", encoding="utf-8")

        record = {
            "url": url,
            "helpjuice_id": page.article_id,
            "category": category,
            "slug": slug,
            "title": title,
            "file": str(article_file.relative_to(args.out)),
            "source": None,
            "similarity": None,
        }

        source = sources.get(normalize_title(title))
        if source:
            matched_sources.add(source)
            source_lines = markdown_to_lines(source.read_text(encoding="utf-8", errors="replace"))
            helpjuice_lines = html_to_lines(page.body)
            record["source"] = str(source.relative_to(args.sources))
            record["similarity"] = round(
                difflib.SequenceMatcher(None, " ".join(source_lines), " ".join(helpjuice_lines)).ratio(), 3
            )
            if source_lines != helpjuice_lines:
                diff_file = args.out / "diffs" / category / f"{slug}.diff"
                diff_file.parent.mkdir(parents=True, exist_ok=True)
                diff_file.write_text(
                    "\n".join(
                        difflib.unified_diff(
                            source_lines, helpjuice_lines,
                            fromfile=f"git/{record['source']}", tofile=f"helpjuice/{category}/{slug}",
                            lineterm="",
                        )
                    ) + "\n",
                    encoding="utf-8",
                )
                record["diff"] = str(diff_file.relative_to(args.out))

        records.append(record)
        print(f"[{number}/{len(urls)}] {url} -> {record['source'] or 'no source'}", file=sys.stderr)
        time.sleep(args.delay)

    (args.out / "articles.json").write_text(json.dumps(records, indent=2) + "\n", encoding="utf-8")
    write_report(args, records, sources, matched_sources)
    print(f"Report: {args.out / 'report.md'}", file=sys.stderr)
    return 0


def write_report(args, records, sources, matched_sources) -> None:
    changed = sorted((r for r in records if r.get("diff")), key=lambda r: r["similarity"])
    unchanged = [r for r in records if r["source"] and not r.get("diff")]
    missing = [r for r in records if not r["source"]]
    lines = [
        f"# Helpjuice extraction report: {args.version}",
        "",
        f"- Articles published on Helpjuice: {len(records)}",
        f"- Identical to the Git sources (normalized text): {len(unchanged)}",
        f"- Different between Helpjuice and Git: {len(changed)}",
        f"- Without a matching Git source (created or renamed on Helpjuice): {len(missing)}",
    ]
    if args.sources:
        only_git = sorted(set(sources.values()) - matched_sources)
        lines.append(f"- Git sources without a Helpjuice article (deleted or renamed on Helpjuice): {len(only_git)}")
    lines += ["", "## Different between Helpjuice and Git", "",
              "Lowest similarity first. A difference can come from an edit on Helpjuice after the migration,",
              "or from a Git change that never reached Helpjuice: check the Git history of the source.", "",
              "| Similarity | Helpjuice article | Git source | Diff |", "|---|---|---|---|"]
    lines += [f"| {r['similarity']} | [{r['title']}]({r['url']}) | `{r['source']}` | `{r['diff']}` |" for r in changed]
    lines += ["", "## Without a matching Git source", "", "| Helpjuice article | Saved body |", "|---|---|"]
    lines += [f"| [{r['title']}]({r['url']}) | `{r['file']}` |" for r in missing]
    if args.sources:
        lines += ["", "## Git sources without a Helpjuice article", ""]
        lines += [f"- `{p.relative_to(args.sources)}`" for p in only_git]
    (args.out / "report.md").write_text("\n".join(lines) + "\n", encoding="utf-8")


if __name__ == "__main__":
    sys.exit(main())
