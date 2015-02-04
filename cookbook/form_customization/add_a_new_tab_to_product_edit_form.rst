How to Add a New Tab on Akeneo entities forms
=============================================

To add or override a tab on an Akeneo PIM edit form, you can use our tab system based on tagged services.
For each tab of our entities' form we have a tagged service to handle its rendering. You can find them on the
``src/Pim/Bundle/EnrichBundle/Resources/config/view`` folder.

How to overriding a tab template
--------------------------------

If you don't need to add custom logic, you can override only the template parameter.

For example to use your own template for the product category tab you can override this parameter in your ``services.yml`` file:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/view/product.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/view/product.yml
    :linenos:

This way you can replace the original template or extend it. As you have access to the context of the parent page,
you can even have access to the form, the user, etc.

.. note::
     As we add an HTML comment at the beginning of each tab content, you can easily find the original location of the template by inspecting the HTML code in your browser web tools.

How to overriding the tab logic
-------------------------------

If you need to override the tab logic you can override the related tagged service in your bundle:

.. code-block:: yaml
    :emphasize-lines: 4
    :linenos:

    services:
        pim_enrich.view_element.attribute.tab.parameter:
            class: Acme\Bundle\EnrichBundle\View\Tab\MyCustomTab
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

You can decide to display your tab only to a category of user or only on form edit, etc. To ease this process, we created some standard
visibility checkers:

.. code-block:: yaml
    :linenos:

    # To check if we are in an edit form
    - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.edit_form'] ]
    # Does the user have the right to view this tab according to this acl ?
    - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.acl', {acl: 'pim_enrich_category_history'}] ]
    # Does the given property exists ?
    - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.non_empty_property', {property: '[form][operation].vars[data].warningMessages'}] ]
    # Ask to voters if we have the right for the given attribute on the given object
    - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.voter', {attribute: 'edit_cateogry', object: '[form][operation].vars[data].product'}] ]


Each view can register visibility checker to check if the view should be visible. To register a visibility checker you have to register it in the container as a service and add a call to your tab:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/view/category.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/view/category.yml
    :emphasize-lines: 9
    :lines: 1-11
    :linenos:

You can also add yours by creating a service implementing the ``Pim\Bundle\EnrichBundle\ViewElement\Checker\VisibilityCheckerInterface``.


How to add your own tab to a form
---------------------------------

You can also register a new tab on a form. To do so, you need to register a tab service and set the position of this tab:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/view/category.yml
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
