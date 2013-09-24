Akeneo PIM Documentation
========================

The Akeneo PIM documentation

Installation
------------

Clone this repository.

Download and install [Sphinx](http://sphinx-doc.org/).
```bash
    $ sudo apt-get install python-setuptools python-dev build-essential
```

Download composer `curl -s https://getcomposer.org/installer | php` and run `php composer.phar install`.

Build the documention
---------------------

From the `./pim-docs` directory, run:

``` bash
    $ sphinx-build -b html . ../pim-docs-build
```

The documentation will be generated inside `../pim-docs-build`.
