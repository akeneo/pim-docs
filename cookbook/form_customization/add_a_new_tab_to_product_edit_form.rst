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

- ``(AcmeCatalogBundle)/Resources/views/Product/_navbar.html.twig``
- ``(AcmeCatalogBundle)/Resources/views/Product/_tab-panes.html.twig``

.. code-block:: html+jinja
    :linenos:

    {# _navbar.html.twig #}
    {% set form_tabs=['Attributes'] %}

    {% if resource_granted('pim_catalog_product_categories_view') %}
        {% set form_tabs = form_tabs|merge(['Categories']) %}
    {% endif %}

    {% if resource_granted('pim_catalog_associations_view') %}
        {% set form_tabs = form_tabs|merge(['Associations']) %}
    {% endif %}

    {# Let's add a new tab here #}
    {% set form_tabs = form_tabs|merge(['Custom tab']) %}

    {% set form_tabs = form_tabs|merge(['Completeness', 'History']) %}
    {{ elements.form_navbar(form_tabs) }}

.. code-block:: html+jinja
    :linenos:

    {# _tab-panes.html.twig #}
    <!-- Original content -->
    <div class="tab-pane active" id="attributes">
        {% include 'PimCatalogBundle:Product:_attributes.html.twig' %}
    </div>
    {% if resource_granted("pim_catalog_product_categories_view") %}
        <div class="tab-pane" id="categories">
           {% include 'PimCatalogBundle:Product:_associateCategories.html.twig' %}
        </div>
    {% endif %}

    {% if resource_granted("pim_catalog_associations_view") %}
        <div class="tab-pane" id="associations" data-url="{{ path('pim_catalog_associations', { id: product.id }) }}">
        </div>
    {% endif %}

    <div class="tab-pane" id="completeness" data-url="{{ path('pim_catalog_product_completeness', { id: product.id }) }}">
    </div>

    <div class="tab-pane" id="history" data-url="{{ path('pim_catalog_product_history', { id: product.id }) }}">
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
``elements.form_navbar``.
