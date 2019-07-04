.. _contribute_to_docs:

How to enhance the documentation?
=================================

How to contribute
-----------------

Want to help improve the Documentation?

The Akeneo PIM documentation uses ``reStructuredText`` as its markup language and
``Sphinx`` for building the output (HTML, PDF, ...).

We're very interested in tutorials or cookbooks to explain how to customize the PIM to fit your project's needs.

Before contributing on a new entry, please begin by creating an `Issue`_ on GitHub to explain your idea.

.. _Issue: https://github.com/akeneo/pim-docs/issues

For typo / quick fixes you can directly submit a `PullRequest`_.

.. _PullRequest: https://help.github.com/en/articles/about-pull-requests

To test the rendering of the documentation you can follow this `HowTo`_.

.. _HowTo: https://github.com/akeneo/pim-docs/blob/master/README.md

Once your Pull Request is merged, don't hesitate to claim your badge "Core contributor" on `Badger platform <http://badger.akeneo.com/login/>`_!

reStructuredText
----------------

reStructuredText "is an easy-to-read, what-you-see-is-what-you-get plaintext
markup syntax and parser system".

You can learn more about its syntax by reading existing Akeneo PIM `documents`_
or by reading the `reStructuredText Primer`_ on the Sphinx website.

If you are familiar with Markdown, be careful as things are sometimes very
similar but different:

* Lists start at the beginning of a line (no indentation is allowed);
* Inline code blocks use double-ticks (````like this````).

Sphinx
------

`Sphinx`_ is a build system that adds some nice tools to create documentation
from reStructuredText documents. As such, it adds new directives and
interpreted text roles to standard reST `markup`_.

Syntax Highlighting
~~~~~~~~~~~~~~~~~~~

All code examples use PHP as the default highlighted language. You can change
it with the ``code-block`` directive:

.. code-block:: rst

    .. code-block:: yaml

    { foo: bar, bar: { foo: bar, bar: baz } }

If your PHP code begins with ``<?php``, then you need to use ``html+php`` as the highlighted pseudo-language:

.. code-block:: rst

    .. code-block:: html+php

    <?php echo $this->foobar(); ?>

.. note::

    A list of supported languages is available on the `Pygments website`_.

    .. _docs-configuration-blocks:

Configuration Blocks
~~~~~~~~~~~~~~~~~~~~

Whenever you show a configuration, you must use the ``configuration-block``
directive to show the configuration in all supported configuration formats
(``PHP``, ``YAML``, and ``XML``)

.. code-block:: rst

    .. configuration-block::

    .. code-block:: yaml

        # Configuration in YAML

    .. code-block:: xml

        <!-- Configuration in XML //-->

    .. code-block:: php

        // Configuration in PHP

The previous reST snippet renders as follows:

.. configuration-block::

.. code-block:: yaml

    # Configuration in YAML

.. code-block:: xml

    <!-- Configuration in XML //-->

.. code-block:: php

    // Configuration in PHP

The current list of supported formats is the following:

+-----------------+-------------+
| Markup format   | Displayed   |
+=================+=============+
| html            | HTML        |
+-----------------+-------------+
| xml             | XML         |
+-----------------+-------------+
| php             | PHP         |
+-----------------+-------------+
| yaml            | YAML        |
+-----------------+-------------+
| jinja           | Twig        |
+-----------------+-------------+
| html+jinja      | Twig        |
+-----------------+-------------+
| html+php        | PHP         |
+-----------------+-------------+
| ini             | INI         |
+-----------------+-------------+
| php-annotations | Annotations |
+-----------------+-------------+

Adding Links
~~~~~~~~~~~~

To add links to other pages in the documents use the following syntax:

.. code-block:: rst

    :doc:`/path/to/page`

Using the path and filename of the page without the extension, for example:

.. code-block:: rst

    :doc:`/book/architecture`

    :doc:`/bundles/FooBundle/installation`

The link text will be the main heading of the document linked to. You can
also specify alternative text for the link:

.. code-block:: rst

    :doc:`</bundles/FooBundle/installation>`

You can also add links to the PHP documentation:

.. code-block:: rst

    :phpclass:`SimpleXMLElement`

    :phpmethod:`DateTime::createFromFormat`

    :phpfunction:`iterator_to_array`

.. _Sphinx:                  https://www.sphinx-doc.org/
.. _documents:               https://github.com/akeneo/pim-docs
.. _reStructuredText Primer: https://www.sphinx-doc.org/en/master/usage/restructuredtext/basics.html
.. _markup:                  https://www.sphinx-doc.org/en/master/usage/restructuredtext/directives.html
.. _Pygments website:        http://pygments.org/languages/
.. _source:                  https://github.com/fabpot/sphinx-php
.. _Sphinx quick setup:      https://www.sphinx-doc.org/en/master/usage/quickstart.html#setting-up-the-documentation-sources
