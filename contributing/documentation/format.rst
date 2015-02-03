Contributing Documentation
==========================

Get documentation from GitHub
-----------------------------

Using Windows
~~~~~~~~~~~~~
Install GitHub for Windows
Add this project (akeneo/pim-docs) using SSH/HTTP link
Change to branch X.X version
Make your changes directly from your local filesystem (including images)
Comit & Push your changes

Using Linux
~~~~~~~~~~~

Considering you want to update documentation for version x.y :

git clone git@github.com:akeneo/pim-docs.git
git checkout x.y

// Make your changes, using whatever text editor

// See what files you have changed using :
git status

// Get a more detailed view using :
git diff

// Add the files you've just edited in your commit stack :
git add /path/to/your/file.rst

Note : you can using joker * like this :
git add /path/to/your/file/*.rst

if you want to add all of your rst files.

git commit -m "Description of what you added/removed/changed"
git pull origin x.y

Test it
-------


Lorem ipsum dolor sit amet


The Akeneo PIM documentation uses `reStructuredText`_ as its markup language and
`Sphinx`_ for building the output (HTML, PDF, ...).

reStructuredText
----------------

reStructuredText "is an easy-to-read, what-you-see-is-what-you-get plaintext
markup syntax and parser system".

You can learn more about its syntax by reading existing Akeneo PIM `documents`_
or by reading the `reStructuredText Primer`_ on the Sphinx website.

If you are familiar with Markdown, be careful as things are sometimes very
similar but different:

* Lists starts at the beginning of a line (no indentation is allowed);
* Inline code blocks use double-ticks (````like this````).

Sphinx
------

Sphinx is a build system that adds some nice tools to create documentation
from reStructuredText documents. As such, it adds new directives and
interpreted text roles to standard reST `markup`_.

Syntax Highlighting
~~~~~~~~~~~~~~~~~~~

All code examples uses PHP as the default highlighted language. You can change
it with the ``code-block`` directive:

.. code-block:: rst

    .. code-block:: yaml

    { foo: bar, bar: { foo: bar, bar: baz } }

    If your PHP code begins with ``<?php``, then you need to use ``html+php`` as
    the highlighted pseudo-language:

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

The previous reST snippet renders as follow:

.. configuration-block::

.. code-block:: yaml

    # Configuration in YAML

.. code-block:: xml

    <!-- Configuration in XML //-->

.. code-block:: php

    // Configuration in PHP

The current list of supported formats are the following:

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

    :doc:`Le Lien </bundles/FooBundle/installation>`

You can also add links to the PHP documentation:

.. code-block:: rst

    :phpclass:`SimpleXMLElement`

    :phpmethod:`DateTime::createFromFormat`

    :phpfunction:`iterator_to_array`

Testing Documentation
~~~~~~~~~~~~~~~~~~~~~

To test documentation before a commit:

* Install `Sphinx`_;

* Run the `Sphinx quick setup`_;

* Install the Sphinx extensions (see below);

* Run ``make html`` and view the generated HTML in the ``build`` directory.

.. _reStructuredText:        http://docutils.sourceforge.net/rst.html
.. _Sphinx:                  http://sphinx-doc.org/
.. _documents:               https://github.com/akeneo/pim-docs
.. _reStructuredText Primer: http://sphinx-doc.org/rest.html
.. _markup:                  http://sphinx-doc.org/markup/
.. _Pygments website:        http://pygments.org/languages/
.. _source:                  https://github.com/fabpot/sphinx-php
.. _Sphinx quick setup:      http://sphinx-doc.org/tutorial.html#setting-up-the-documentation-sources

