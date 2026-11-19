# Toolchains used to build every archived version from its sources.
# Everything is pinned so that the archive keeps building the same way over time.
#
#   modern: versions 4.0 and later (plain Sphinx, as on their original branches)
#   legacy: versions 1.0 to 3.2, which need the sensio.sphinx.* (sphinx-php) and
#           sphinxcontrib.youtube extensions. Those extensions do not load on recent
#           Sphinx releases, so this toolchain sticks to Sphinx 1.8, used by those branches.

FROM python:3.12.7-slim-bookworm AS modern
RUN pip install --no-cache-dir \
        sphinx==7.4.7 \
        docutils==0.21.2 \
        pygments==2.18.0 \
        jinja2==3.1.4
WORKDIR /docs

FROM python:3.7.17-slim-bullseye AS legacy
# sphinx-php is pinned to v1.0.10 (f1b69df), the last release shipping sensio.sphinx.refinclude,
# which versions 1.0 to 2.3 use. It is the release their original Dockerfiles installed.
# setuptools < 70 still ships pkg_resources, which the legacy extensions rely on.
RUN pip install --no-cache-dir setuptools==57.5.0 wheel==0.37.1 \
 && pip install --no-cache-dir --no-build-isolation \
        sphinx==1.8.6 \
        docutils==0.17.1 \
        pygments==2.5.2 \
        jinja2==2.11.3 \
        markupsafe==1.1.1 \
        alabaster==0.7.12 \
        https://github.com/fabpot/sphinx-php/archive/f1b69df331217d34425aa09821449e08fa219d38.zip \
        https://github.com/mickaelandrieu/sphinxcontrib.youtube/archive/3eea24ce31515bdad52bae41a8d4ce7dc7b9b7f6.zip
WORKDIR /docs
