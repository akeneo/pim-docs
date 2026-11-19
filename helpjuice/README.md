# Bringing the Helpjuice changes back into Git

## What is on Helpjuice

help.akeneo.com runs on Helpjuice since the spring of 2023. Before that, it was a static site
generated from Git repositories. The commits "update link PIM Helpjuice" of
[akeneo/pim-helpcenter](https://github.com/akeneo/pim-helpcenter) (2023-04-03/04) date the switch.

| Helpjuice categories (help.akeneo.com/en_US/...) | Published articles (2026-11) | Former Git source |
|---|---|---|
| `v7`, `v7-*` | 140 | `akeneo/pim-helpcenter`, branch `v7` (`content/md`) |
| `v6`, `v6-*` | 143 | `akeneo/pim-helpcenter`, branch `v6` |
| `v5`, `v5-*` | 140 | `akeneo/pim-helpcenter`, branch `v5` |
| `v4`, `v4-*` | 127 | `akeneo/pim-helpcenter`, branch `v4` |
| `v3`, `v3-*` | 103 | `akeneo/pim-helpcenter`, branch `v3` |
| `v2`, `v2-*` | 93 | `akeneo/pim-helpcenter`, branch `v2` |
| `v1-3-*` to `v1-7-*` | a few | no branch left in `akeneo/pim-helpcenter` |
| `serenity-*` | | `akeneo/pim-helpcenter`, branch `master` (SaaS, out of scope) |
| `akeneo-portal-project-console-flexibility` | | `akeneo/portal-helpcenter` (archived) |

The functional documentation of the On-Premise and Flexibility versions (the "v2" to "v7"
categories) is therefore the Helpjuice copy of the `vN` branches of `akeneo/pim-helpcenter`.

The technical documentation of this repository (`akeneo/pim-docs`, archived in `versions/`) was
not moved to Helpjuice: docs.akeneo.com kept being served from its own server, and only its links
to the help center were updated after the migration (commits `eac15522`, `a9c08ba5`). If some of
its pages were nevertheless copied into Helpjuice by hand, the same method applies: point
`--sources` to a directory of sources and adapt `load_sources()` to read RST titles.

## Method 1: public extraction and comparison (no credentials)

[`extract.py`](extract.py) only reads the public site and uses only the Python standard library:

1. it lists the articles of a version from `https://help.akeneo.com/sitemap.xml`,
2. it saves the HTML body of each article (`div.helpjuice-article-body-content`) and its
   Helpjuice id,
3. it matches each article with the Markdown source of the same title (front matter `title:`),
4. it compares their text sentence by sentence, ignoring what the migration changed without
   changing the content (re-wrapped paragraphs, upper-cased headings, callouts, emoji),
5. it writes a report: articles that differ, articles without a Git source (created or renamed on
   Helpjuice), Git sources without an article (deleted or renamed on Helpjuice).

```bash
# Sources of the version, exported from Git
mkdir -p /tmp/hc-v7
git -C ../pim-helpcenter archive origin/v7 content/md | tar -x -C /tmp/hc-v7

python3 -I helpjuice/extract.py --version v7 --out /tmp/hj-v7 --sources /tmp/hc-v7/content/md
less /tmp/hj-v7/report.md
```

First run, version v7 (2026-11-19): 140 articles, 44 identical to Git, 94 different, 2 without a
Git source, 13 Git sources without an article.

A difference does not tell which side changed. Some v7 differences are Helpjuice edits; others are
Git changes that never reached Helpjuice (for instance "the SKU is now optional" exists only in
Git). For each different article, check the Git history of its source since the migration:

```bash
git -C ../pim-helpcenter log --since=2023-03-01 --oneline origin/v7 -- content/md/<source>
```

Some articles are published in several Helpjuice categories (for instance "Manage your views").
They show up once per category in the report and share the same Git source.

## Method 2: Helpjuice API v3 (needs an API key)

The API answers on the help center domain: `GET https://help.akeneo.com/api/v3/articles` and
`GET https://help.akeneo.com/api/v3/categories` exist (they return `403 Not allowed` without a
key). A Helpjuice administrator can create a key in the Helpjuice settings (API credentials).

Compared to the public extraction, the API also returns unpublished and draft articles and the
dates of the articles. That makes it possible to keep only the articles updated after the
migration. The parameters and field names were not checked here, because that needs a key. Check
them in the API documentation linked from the Helpjuice admin, then replace `article_urls()` and
`ArticleParser` in `extract.py` with API calls. The rest of the script (matching, comparison,
report) stays the same.

No public endpoint exposes the revision history of an article. To see who changed what on an
article, use its history in the Helpjuice editor.

## Bringing a change back into Git

For each article to bring back:

1. Convert its saved body to Markdown, keeping the structure:

   ```bash
   docker run --rm -v /tmp/hj-v7:/data pandoc/core:3.5 \
       -f html -t gfm --wrap=none \
       /data/articles/v7-build-your-catalog/v7-manage-your-attribute-groups.html
   ```

2. Download the images it references. All of them are hosted on `static.helpjuice.com`: put them
   in the `img/` directory next to the source and replace the URLs with relative paths.
3. Restore the front matter of the source (`id`, `themes`, `title`, `popular`), the `:::info`,
   `:::tips` and `:::warning` blocks (Helpjuice callouts) and the emoji shortcodes, so that the
   help center generator still renders the page.
4. Open one pull request per Helpjuice category on the `vN` branch, linking the Helpjuice article
   in the description.

Articles without a Git source become new Markdown files. For Git sources without an article,
decide whether the page was renamed (it then appears among the articles without a source) or
removed on purpose.
