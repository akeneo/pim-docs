How to Add a New Tab on Akeneo entities forms
=============================================

To add or override a tab on an Akeneo PIM edit form, you can use our view element system based on tagged services.
For each tab of our entities' form we have a tagged service to handle its rendering. You can find them in the
``src/Pim/Bundle/EnrichBundle/Resources/config/view_elements`` folder.

How to override a view element template
---------------------------------------

If you don't need to add custom logic, you can override only the template parameter.

For example, to use your own template for the product category tab you can override this parameter in your ``services.yml`` file:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/view_elements/product.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/view_elements/product.yml
    :linenos:

This way you can replace the original template or extend it. As you have access to the context of the parent page,
you can even have access to the form, the user, etc.

.. note::
     As we add a HTML comment at the beginning of each tab content, you can easily find the original location of the template by inspecting the HTML code in your browser web tools.

How to override the view element logic
--------------------------------------

If you need to override the view element logic you can override the related tagged service in your bundle:

.. code-block:: yaml
    :emphasize-lines: 4
    :linenos:

    services:
        pim_enrich.view_element.attribute.tab.parameter:
            class: Acme\Bundle\EnrichBundle\ViewElement\Tab\MyCustomTab
            arguments:
                # The tab title
                - 'pim_enrich.attribute.tab.parameter'
                # The tab template location
                - 'AcmeEnrichBundle:Product:Tab/my_category.html.twig'
            tags:
                # Register this view element on the attribute edit form
                - { name: pim_enrich.view_element, type: pim_enrich_attribute_form.form_tab, position: 90 }

You have to implement the ``Pim\Bundle\EnrichBundle\ViewElement\ViewElementInterface`` in order to register it.

How to add a visibility checker
-------------------------------

You can decide to display your view element only to a category of users or only on form edit, etc. To ease this process, we created some standard
visibility checkers:

.. code-block:: yaml
    :linenos:

    # To check if we are in an edit form
    - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.edit_form'] ]
    # Does the user have the right to view this tab according to this acl?
    - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.acl', {acl: 'pim_enrich_category_history'}] ]
    # Does the given property exist?
    - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.non_empty_property', {property: '[form][operation].vars[data].warningMessages'}] ]
    # Ask to voters if we have the right for the given attribute on the given object
    - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.voter', {attribute: 'edit_cateogry', object: '[form][operation].vars[data].product'}] ]


Each view can register visibility checkers to check if the view should be visible. To register a visibility checker you have to register it in the container as a service and add a call to your tab:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/view_elements/category.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/view_elements/category.yml
    :emphasize-lines: 9
    :lines: 1-11
    :linenos:

You can also add yours by creating a service implementing the ``Pim\Bundle\EnrichBundle\ViewElement\Checker\VisibilityCheckerInterface``.


How to add your own tab to a form
---------------------------------

You can also register a new tab on a form. To do so, you need to register a new view element service, tag it as a tab for the corresponding form and set the position of this tab:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/view_elements/category.yml
    services:
        pim_enrich.view_element.category.tab.my_custom_tab:
            parent: pim_enrich.view_element.base
            arguments:
                - 'pim_enrich.category.tab.my_custom_tab_title'
                - 'AcmeEnrichBundle:Category:Tab/my_custom_tab.html.twig'
            calls:
                - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.edit_form'] ]
            tags:
                - { name: pim_enrich.view_element, type: pim_category.form_tab, position: 120 }

Example
-------

Let say that we would like to add a tab on the product edit form to manage shipping package sizes for our client. This tab will be visible only if the user has the right to see it and we would like to put it between the attribute tab and the category tab.

First of all, we need to register this tab in our ``service.yml`` file:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/service.yml
    services:
        # You can name your service as you want but it's always better to follow conventions
        acme_enrich.view_element.product.tab.package_management:
            parent: pim_enrich.view_element.base
            arguments:
                - 'package_management' # this is the translation key for our tab title
                - 'AcmeEnrichBundle:Product:Tab/package_management.html.twig' # The location of your template
            tags:
                # The attribute tab is at the 90 position and the category one is at position 100.
                - { name: pim_enrich.view_element, type: pim_product_edit.form_tab, position: 95 }

You can now create your template at ``/src/Acme/Bundle/EnrichBundle/Resources/views/Product/Tab/package_management.html.twig``

.. code-block:: jinja
    :linenos:

    {# /src/Acme/Bundle/EnrichBundle/Resources/views/Product/Tab/package_management.html.twig #}
    <h1>Hello world!</h1>

    {{ dump(form) }}

After a cache clear (``app/console cache:clear``) you should see something like this on a product edit form:

.. image:: product.png

As you can see, you will have to translate the tab title in your translation file (see http://symfony.com/doc/current/book/translation.html).

As shown in the screenshot above we have total access to the product edit form and we can now render our package section in this tab.

* Apply rights on our tab

To apply rights on our tab we can add a visibility checker to it:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/service.yml
    services:
        acme_enrich.view_element.product.tab.package_management:
            parent: pim_enrich.view_element.base
            arguments:
                - 'package_management'
                - 'AcmeEnrichBundle:Product:Tab/package_management.html.twig'
            calls:
                - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.acl', {acl: 'acme_enrich_product_package_management'}] ]
            tags:
                - { name: pim_enrich.view_element, type: pim_product_edit.form_tab, position: 95 }

To add the ``acme_enrich_product_package_management`` ACL you can refer to :doc:`/cookbook/acl/define-acl`

* Display the tab only on edit

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/service.yml
    services:
        acme_enrich.view_element.product.tab.package_management:
            parent: pim_enrich.view_element.base
            arguments:
                - 'package_management'
                - 'AcmeEnrichBundle:Product:Tab/package_management.html.twig'
            calls:
                - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.acl', {acl: 'acme_enrich_product_package_management'}] ]
                - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.edit_form'] ]
            tags:
                - { name: pim_enrich.view_element, type: pim_product_edit.form_tab, position: 95 }

And that's it! You can now add everything you want in your tab.
