How to Add a New Tab on the Product Edit Form
=============================================

To add a new tab on the product edit form, you will need to override the
templates for the navbar form (``PimCatalogBundle:Product:_navbar.html.twig``)
and tab panes (``PimCatalogBundle:Product:_tab-panes.html.twig``).

.. _see here: http://symfony.com/doc/current/cookbook/bundles/inheritance.html

In order to do this, you will need to define a bundle that is a child of ``PimCatalogBundle`` (`see here`_).

Overriding the templates
------------------------------
To override these templates, you need to create 2 new files:

- ``(AcmeCatalogBundle)/Resources/views/Product/_navbar.html.twig``
- ``(AcmeCatalogBundle)/Resources/views/Product/_tab-panes.html.twig``

.. code-block:: html+jinja

    {# _navbar.html.twig #}
    {{ elements.form_navbar(['Categories', 'Attributes', 'Completeness', 'History', 'Custom tab']) }}

.. code-block:: html+jinja

    {# _tab-panes.html.twig #}
    <!-- Original content -->
    <div class="tab-pane active" id="attributes">
        {% include 'PimCatalogBundle:Product:_attributes.html.twig' %}
    </div>

    <div class="tab-pane" id="categories">
       {% include 'PimCatalogBundle:Product:_associateCategories.html.twig' %}
    </div>

    <div class="tab-pane" id="completeness">
       {% include 'PimCatalogBundle:Product:_completeness.html.twig' %}
    </div>

    <div class="tab-pane" id="history">
        <div id="history-grid"></div>
    </div>

    <!-- Custom content -->
    <div class="tab-pane" id="custom-tab">
        <div>
            Some custom content here
        </div>
    </div>

.. note::

    Make sure you clear the cache to enable your templates to be loaded.

.. warning::

    For the created tab pane to work, its ``id`` attribute must match the navbar title for this tab pane
    (transformed to lowercase and spaces replaced with dashes)

If you would like to have a different order in the tab panes, simply reorder the arguments passed to
``elements.form_navbar``:

.. code-block:: html+jinja

    {# _navbar.html.twig #}
    {{ elements.form_navbar(['Categories', 'Custom tab', 'Attributes', 'Completeness', 'History']) }}
