# Akeneo PIM Documentation

The Akeneo PIM documentation

## Build the documentation

### With Docker

Install [Docker](https://docs.docker.com/engine/installation/).

From the `./pim-docs` directory, run:

```bash
    $ docker build . --tag pim-docs:master
    $ rm -rf pim-docs-build && mkdir pim-docs-build
    $ docker run --rm \
        -v $(pwd):/home/akeneo/pim-docs/data \
        pim-docs:master \
        ./build.sh master --uid $(id -u) --gid $(id -g) --no-asset-check
```

The docs will be built into `./pim-docs-build`.

### Manually

#### Installation

Clone this repository.

##### Linux

Install [Sphinx](http://sphinx-doc.org/).
```bash
    $ sudo apt-get install python-pip
    $ sudo pip install --upgrade pip
    $ sudo pip install sphinx~=1.5.3
    $ sudo pip install git+https://github.com/fabpot/sphinx-php.git
    $ sudo pip install git+https://github.com/mickaelandrieu/sphinxcontrib.youtube.git
```

##### Mac OS

```bash
    $ brew install python
    $ pip install sphinx
    $ pip install git+https://github.com/fabpot/sphinx-php.git
    $ pip install git+https://github.com/mickaelandrieu/sphinxcontrib.youtube.git
```

> If you encounter the following error ``ValueError: ('Expected version spec in', 'sphinx~=1.5.3', 'at', '~=1.5.3')``
  Use ``pip install --upgrade pip`` before install sphinx

Download composer `curl -s https://getcomposer.org/installer | php` and run `php composer.phar install`.

#### Build the documentation

From the `./pim-docs` directory, run:

``` bash
    $ sphinx-build -b html . ../pim-docs-build
```

The documentation will be generated inside `../pim-docs-build`.

#### Validate the documentation

From the `./pim-docs` directory, run:

``` bash
    $ sphinx-build -nWT -b linkcheck . _build/
```

#### Make documentation code work with pim-community-dev or standard

Install pim-community.

Then, go to Akeneo PIM `src/` directory and create a symlink `Acme` pointing to `pim-docs/src/Acme`.

Add all Acme bundles in `app/AppKernel.php` file.

#### Install the Akeneo Styleguide page

The needed files for Akeneo Styleguide are installed with the `composer install`. When you build the documentation,
you will have an empty page `/styleguide/index.html`. You have to add a RewriteRule on your Apache configuration
to redirect to `/styleguide/index.php`:

```
    RedirectMatch 301 /styleguide/index.html /styleguide/index.php
```

## Deploy the documentation

If you want to deploy all branches, use this script https://github.com/akeneo/dev-tools/blob/master/script-executor/app/scripts/generate-docs.sh.

If you want to deploy only your current branch, you can use the ``build.sh`` script of this repository with the option ``--deploy``.

## Contribution

Don't hesitate to suggest cookbook ideas via https://github.com/akeneo/pim-docs/issues.

## Developer's notes

- The folder `/_themes/sphinx_rtd_theme` is a clone from https://github.com/snide/sphinx_rtd_theme, and was
updated on 2016-07. If you want to customize the Akeneo theme, please only update `/_theme/akeneo_rtd` theme.
