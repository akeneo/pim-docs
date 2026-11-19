How to add a new menu in Akeneo
===============================

For the purpose of this cookbook, we will imagine that we want add Structure menu with the Category and Attribute sub-menus.

How to create the new menu
--------------------------

First of all, we will add our new tab in the main menu.

In ``Pim\Bundle\EnrichBundle\Resources\config\navigation.yml`` :

.. code-block:: yaml

    oro_menu_config:
        items:
            pim_structure_tab:     # Create the tab
                label: Structure   # (Mandatory) Label of your new menu
                uri: '#'           # (Mandatory) URI of your menu
                extras:            # (Optional) Allow you to add some options
                    position: 35   # Position of your tab in the menu

        tree:
            application_menu:
                children:
                    pim_structure_tab: ~  # Add your new tab in the menu

Now, we remove our Symfony cache and refresh our Akeneo page to see the new menu tab.

.. image:: images/cookbook-menu.jpeg
    :width: 654px
    :align: center
    :height: 44px

How to add sub-menus
--------------------

To add sub-menus, we need to know which route we want to add. For the purpose of this guide we will add existing route in sub-menu.

.. note::

    You can add your own route following this `Symfony guide <http://symfony.com/doc/current/book/routing.html>`_.

We can see existing route of Category page and Attribute page in their respective routing file.

``Pim\Bundle\EnrichBundle\Resources\config\routing\categorytree.yml`` :

.. code-block:: yaml

    pim_enrich_categorytree_index:  # The key we need to add
        path: /
        defaults: { _controller: pim_enrich.controller.category_tree:indexAction }

``Pim\Bundle\EnrichBundle\Resources\config\routing\attribute.yml`` :

.. code-block:: yaml

    pim_enrich_attribute_index:     # The key we need to add
        path: /.{_format}
        defaults: { _controller: pim_enrich.controller.attribute:indexAction, _format: html }
        requirements:
            _format: html|json

And tab menu are already created in the EnrichBundle ``Pim\Bundle\EnrichBundle\Resources\config\navigation.yml`` :

.. code-block:: yaml

    oro_menu_config:
        items:
            pim_enrich_attribute:                             # Key of the tab attribute
                label: pim_menu.item.attribute                # Translation key of the label
                route: pim_enrich_attribute_index             # Id of the route we seen in the previous file
                aclResourceId: pim_enrich_attribute_index     # Id of the ACL to check
                extras:
                    routes: ['/^pim_enrich_attribute_\w+$/']  # Pattern which can follow this route
            pim_enrich_categorytree:                          # Key of the tab category
                label: pim_menu.item.category
                route: pim_enrich_categorytree_index
                aclResourceId: pim_enrich_category_list
                extras:
                    routes: ['/^pim_enrich_categorytree_\w+$/']
                    position: 20

Now that we have our keys, go in the first file ``Pim\Bundle\EnrichBundle\Resources\config\navigation.yml`` :

.. code-block:: yaml

    oro_menu_config:
        tree:
        application_menu:
            children:
                pim_structure_tab:  # Remove the ~ we added in the previous step
                    children:
                        pim_enrich_categorytree: ~  # Add keys under a 'children:' option
                        pim_enrich_attribute: ~

We remove our Symfony cache and refresh our Akeneo page to see the new sub-menu tab.

.. image:: images/cookbook-sub-menu.jpeg
    :width: 670px
    :align: center
    :height: 115px
