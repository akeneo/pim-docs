Akeneo PIM Documentation
========================

The Akeneo PIM documentation

Installation
------------

Download and install [Sphinx](http://sphinx-doc.org/).

Download composer and run `php composer.phar install`.

Build the documention
---------------------

From the `./pim-docs` directory, run:

``` bash
    $ sphinx-build -b html . ../pim-docs-build
```

The documentation will be generated inside `../pim-docs-build`.
