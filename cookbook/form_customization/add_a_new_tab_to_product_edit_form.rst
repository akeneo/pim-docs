How to Add a New Tab on the Product Edit Form
=============================================

To add a new tab on the product edit form, you will need to override the
templates for the navbar form (``PimCatalogBundle:Product:_navbar.html.twig``)
and tab panes (``PimCatalogBundle:Product:_tab-panes.html.twig``).

.. _see here: http://symfony.com/doc/current/cookbook/bundles/inheritance.html

In order to do this, you will need to define a bundle that is a child of ``PimCatalogBundle`` (`see here`_).

Overriding the templates
------------------------
To override these templates, you need to create 2 new files:

- ``(AcmeEnrichBundle)/Resources/views/Product/_navbar.html.twig``
- ``(AcmeEnrichBundle)/Resources/views/Product/_tab-panes.html.twig``

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/views/Product/_navbar.html.twig
   :language: jinja
   :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/views/Product/_navbar.html.twig
   :emphasize-lines: 11-12
   :linenos:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/views/Product/_tab-panes.html.twig
   :language: html+jinja
   :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/views/Product/_tab-panes.html.twig
   :emphasize-lines: 22-27
   :linenos:


.. note::

    Make sure you clear the cache to enable your templates to be loaded.

.. warning::

    For the created tab pane to work, its ``id`` attribute must match the navbar title for this tab pane
    (transformed to lowercase and spaces replaced with dashes)

If you would like to have a different order in the tab panes, simply reorder the arguments passed to
``elements.form_navbar``.

