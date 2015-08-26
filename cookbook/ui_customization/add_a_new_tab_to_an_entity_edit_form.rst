How to Add a New Tab to Akeneo entities forms (except product edit form)
========================================================================

To add or override a tab to an Akeneo PIM edit form, you can use our view element system based on tagged services.
For each tab of our entities' form we have a tagged service to handle its rendering. You can find them in the
``src/Pim/Bundle/EnrichBundle/Resources/config/view_elements`` folder.

How to override a view element template
---------------------------------------

If you don't need to add custom logic, you can override only the template parameter.

For example, to use your own template for the é property tab you can override this parameter in your ``services.yml`` file:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/view_elements/category.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/view_elements/category.yml
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
    # Ask voters if we have the permission for the given attribute on the given object
    - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.voter', {attribute: 'edit_category', object: '[form][operation].vars[data].category'}] ]


Each view can register visibility checkers to check if the view should be visible. To register a visibility checker you have to register it in the container as a service and add a call to your tab:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/view_elements/category.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/view_elements/category.yml
    :emphasize-lines: 9
    :lines: 1-11
    :linenos:

You can also add your visibility checkers by creating a service implementing the ``Pim\Bundle\EnrichBundle\ViewElement\Checker\VisibilityCheckerInterface``.


How to add your own tab to a form
---------------------------------

You can also register a new tab on a form. To do so, you need to register a new view element service, tag it as a tab for the corresponding form and set the position of the tab:

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

Let's say that we would like to add a tab to the category edit form to manage custom attributes for our client. This tab will be visible only if the user has the right to see it and we would like to put it between the property tab and the history tab.

First of all, we need to register this tab in our ``service.yml`` file:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/service.yml
    services:
        # You can name your service what you want but it's always better to follow conventions
        acme_enrich.view_element.category.tab.attribute:
            parent: pim_enrich.view_element.base
            arguments:
                - 'attribute' # this is the translation key for our tab title
                - 'AcmeEnrichBundle:Category:Tab/attribute.html.twig' # The location of your template
            tags:
                # The proerty tab is at the 90th position and the history's one is at position 100.
                - { name: pim_enrich.view_element, type: pim_category_edit.form_tab, position: 95 }

You can now create your template ``/src/Acme/Bundle/EnrichBundle/Resources/views/Category/Tab/attribute.html.twig``

.. code-block:: jinja
    :linenos:

    {# /src/Acme/Bundle/EnrichBundle/Resources/views/Category/Tab/attribute.html.twig #}
    <h1>Hello world!</h1>

    {{ dump(form) }}

After a cache clear (``app/console cache:clear``) you should see something like this on a category edit form:

.. image:: product.png #TODO

As you can see, you will have to translate the tab title in your translations file (see http://symfony.com/doc/current/book/translation.html).

As shown in the screenshot above, we have total access to the category edit form and we can now render our package section in this tab.

* Apply rights on our tab

To apply rights on our tab we can add a visibility checker to it:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/service.yml
    services:
        acme_enrich.view_element.category.tab.attribute:
            parent: pim_enrich.view_element.base
            arguments:
                - 'attribute'
                - 'AcmeEnrichBundle:Category:Tab/attribute.html.twig'
            calls:
                - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.acl', {acl: 'acme_enrich_category_attribute'}] ]
            tags:
                - { name: pim_enrich.view_element, type: pim_category_edit.form_tab, position: 95 }

To add the ``acme_enrich_category_attribute`` ACL you can refer to :doc:`/cookbook/acl/define-acl`

* Display the tab only on edit

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/service.yml
    services:
        acme_enrich.view_element.category.tab.attribute:
            parent: pim_enrich.view_element.base
            arguments:
                - 'attribute'
                - 'AcmeEnrichBundle:Category:Tab/attribute.html.twig'
            calls:
                - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.acl', {acl: 'acme_enrich_category_attribute'}] ]
                - [ addVisibilityChecker, ['@pim_enrich.view_element.visibility_checker.edit_form'] ]
            tags:
                - { name: pim_enrich.view_element, type: pim_category_edit.form_tab, position: 95 }

And that's it! You can now add everything you want to your tab.
