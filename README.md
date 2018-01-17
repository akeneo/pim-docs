Akeneo PIM Documentation
========================

The Akeneo PIM documentation

Installation
------------

Clone this repository.

Install [Sphinx](http://sphinx-doc.org/).
```bash
    $ sudo apt-get install python-sphinx
```

Download composer `curl -s https://getcomposer.org/installer | php` and run `php composer.phar install`.

Build the documention
---------------------

From the `./pim-docs` directory, run:

``` bash
    $ sphinx-build -b html . ../pim-docs-build
```

The documentation will be generated inside `../pim-docs-build`.

Make documentation code works with pim-community-dev or standard
----------------------------------------------------------------

Install pim-community

Then go on Akeneo PIM `src/` directory and create a symlink `Acme` pointing on `pim-docs/src/Acme`.

Add all Acme bundles in `app/AppKernel.php` file.

Contribution
------------

Don't hesitate to propose cookbook entries via the https://github.com/akeneo/pim-docs/issues

Build the documentation with Docker
-----------------------------------

Install [Docker](https://docs.docker.com/engine/installation/).

[optional] To update the branch list with the current pim-docs branches, use

```
sed -i -e "s/^\(.*\)'versions': .*,\(.*\)$/\1'versions': ['$(git branch -l|grep -x "\(^[ *]\+[0-9]\+\.[0-9]\+.*\)\|\(^[ *]\+master\)" | cut -c 3- | sort -r | paste -sd " ")'],\2/" conf.py
```

From the `./pim-docs` directory, run:

```bash
    $ docker build . --tag pim-docs:1.2
    $ rm -rf pim-docs-build && mkdir pim-docs-build
    $ docker run --rm \
        -v $(pwd):/home/akeneo/pim-docs/data \
        pim-docs:1.2 \
        ./build.sh --uid $(id -u) --gid $(id -g)
```

The docs will be built into `./pim-docs-build`.
