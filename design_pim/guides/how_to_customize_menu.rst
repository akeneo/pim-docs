How to customize the Back Office menu
=====================================

The Akeneo menu is using the view extension architecture and is basically a tree of backbone views. You can use the one that we provide with the PIM (tab and item) or develop your own.

Define a simple node at the root of the menu
********************************************

To add an element at the root of the tree you can reuse the tab module provided by the pim as follow:

.. code-block: yaml

    # src/Acme/Bundle/AppBundle/Resources/form_extension/menu.yml
    acme-custom-root-element:                          # The unique key of your extension
        module: pim/menu/tab                           # The module provided by akeneo for root elements
        parent: pim-menu                               # The parent is the root of the menu
        aclResourceId: my_custom_acl_key               # [optional] You can define a acl check
        position: 110                                  # [optional] The position in the tree where you want to add the item
        config:
            title: pim_menu.item.import_profile        # You can define a translation key for the tab name

After running the command ``bin/console pim:install:asset`` your new item should appear at the root of the menu

Define a simple node inside a tab of the menu
*********************************************

Now if you want to add an element inside the menu, you can use the item module:

.. code-block: yaml

    # src/Acme/Bundle/AppBundle/Resources/form_extension/menu.yml
    acme-custom-sub-element:                           # The unique key of your extension
        module: pim/menu/item                          # The module provided by akeneo for sub elements
        parent: acme-custom-root-element               # The parent is the tab we just created
        targetZone: item
        aclResourceId: my_custom_acl_key               # [optional] You can define a acl check
        position: 110                                  # [optional] The position in the tree where you want to add the item
        config:
            title: pim_menu.item.import_profile        # You can define a translation key for the item name
            to: pim_importexport_import_profile_index  # The route to redirect to

After running the command ``bin/console pim:install:asset`` your new item should appear in the menu

Use you own menu extension item
*******************************

As you may have already guessed, with this system, you can develop your own menu item and add custom informations like notification badges or custom display.
