# Akeneo PIM Documentation

The Akeneo PIM documentation

## Installation

Clone this repository.

### Linux

Install [Sphinx](http://sphinx-doc.org/).
```bash
    $ sudo apt-get install python-sphinx
    $ sudo pip install git+https://github.com/fabpot/sphinx-php.git
```

### Mac OS

```bash
    $ brew install python
    $ pip install sphinx
    $ pip install git+https://github.com/fabpot/sphinx-php.git
```

Download composer `curl -s https://getcomposer.org/installer | php` and run `php composer.phar install`.

## Build the documentation

From the `./pim-docs` directory, run:

``` bash
    $ sphinx-build -b html . ../pim-docs-build
```

The documentation will be generated inside `../pim-docs-build`.

## Make documentation code work with pim-community-dev or standard

Install pim-community

Then, go to Akeneo PIM `src/` directory and create a symlink `Acme` pointing to `pim-docs/src/Acme`.

Add all Acme bundles in `app/AppKernel.php` file.

## Contribution

Don't hesitate to suggest cookbook ideas via https://github.com/akeneo/pim-docs/issues

## Developer's notes

- The folder `/_themes/sphinx_rtd_theme` is a clone from https://github.com/snide/sphinx_rtd_theme, and was
updated on 2016-07. If you want to customize the Akeneo theme, please only update `/_theme/akeneo_rtd` theme.

### Build the documentation with Docker

Install [Docker](https://docs.docker.com/engine/installation/).

[optional] To update the branch list with the current pim-docs branches, use

```
sed -i -e "s/^\(.*\)'versions': .*,\(.*\)$/\1'versions': ['$(git branch -l|grep -x "\(^[ *]\+[0-9]\+\.[0-9]\+.*\)\|\(^[ *]\+master\)" | cut -c 3- | sort -r | paste -sd " ")'],\2/" conf.py
```

From the `./pim-docs` directory, run:

```bash
    $ docker build . --tag pim-docs:1.6
    $ rm -rf pim-docs-build && mkdir pim-docs-build
    $ docker run --rm \
        -v $(pwd):/home/akeneo/pim-docs/data \
        pim-docs:1.6 \
        ./build.sh 1.6 --uid $(id -u) --gid $(id -g)
```

The docs will be built into `./pim-docs-build`.
