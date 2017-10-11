How to customize the main menu
==============================

The Akeneo menu is using the view extension architecture and is basically a tree of backbone views. You can use the one that we provide with the PIM (tab and item) or develop your own.

Define a simple node at the root of the menu
********************************************

To add an element at the root of the tree you can reuse the tab module provided by the pim as follow:

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/form_extension/menu.yml
    acme-custom-root-element:                          # The unique key of your extension
        module: pim/menu/tab                           # The module provided by akeneo for root elements
        parent: pim-menu                               # The parent is the root of the menu
        aclResourceId: my_custom_acl_key               # [optional] You can define a acl check
        position: 110                                  # [optional] The position in the tree where you want to add the item
        config:
            title: pim_menu.item.import_profile        # You can define a translation key for the tab name

After running the command ``rm -rf var/cache; bin/console pim:install:asset; yarn run webpack`` your new item should appear at the root of the menu.

Define a simple node inside a tab of the menu
*********************************************

Now if you want to add an element inside the menu, you can use the item module:

.. code-block:: yaml

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

After running the command ``rm -rf var/cache; bin/console pim:install:asset; yarn run webpack`` your new item should appear in the menu.

Highlight menu elements
***********************

If you want the menu to be highlighted on your custom pages, you have to configure a new form extension in your custom page. The module responsible of highlighting the menu is `pim/common/breadcrumbs`.
This module will both display the breadcrumbs and highlight the menu. You simply have to respectively configure its `tab` and `item` to the menu and sub menu declared above to automatically get the labels and links from the menu.

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/form_extension/custom_page.yml
    acme-custom-page:
        module: acme/custom_page

    acme-custom-page-breadcrumbs:
        module: pim/common/breadcrumbs
        parent: acme-custom-page
        targetZone: breadcrumbs
        config:
            tab: acme-custom-root-element
            item: acme-custom-sub-element

After running the command ``rm -rf var/cache; bin/console pim:install:asset; yarn run webpack`` the menu will be highlited when you will open your custom page.

Use you own menu extension item
*******************************

As you may have already guessed, with this system, you can develop your own menu item and add custom informations like notification badges or custom display.
