# Akeneo PIM Documentation

The Akeneo PIM documentation

## Installation

Clone this repository.

### Linux

Install [Sphinx](http://sphinx-doc.org/).
```bash
    $ sudo pip install sphinx~=1.5.3
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
