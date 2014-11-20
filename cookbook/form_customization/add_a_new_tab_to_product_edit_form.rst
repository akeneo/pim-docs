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

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/view/attribute.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/view/attribute.yml
   :emphasize-lines: 4
   :linenos:


You have to implement the ``Pim\Bundle\EnrichBundle\ViewElement\ViewElementInterface`` in order to register it.

How to add a visibility checker
-------------------------------

Each view can register visibility checker to check if the view should visible. You can add yours by creating a service implementing the ``Pim\Bundle\EnrichBundle\ViewElement\Checker\VisibilityCheckerInterface``. To register a visibility checker you have to register it in the container as a service and add a call to your tab:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/view/category.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/view/category.yml
   :emphasize-lines: 9
   :lines: 1-11
   :linenos:


How to add your own tab to a form
---------------------------------

You can also register a new tab on a form. To do so, you need to register a tab service and set the position of this tab:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/view/category.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/view/category.yml
   :emphasize-lines: 4,11
   :lines: 1,13-
   :linenos:
