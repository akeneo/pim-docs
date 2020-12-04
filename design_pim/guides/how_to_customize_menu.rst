How to customize the main menu
==============================

The Akeneo menu is using the view extension architecture and is basically a tree of backbone views. You can use the one that we provide with the PIM (tab and item) or develop your own.

Define a simple node at the root of the menu
********************************************

To add an element at the root of the tree you can reuse the tab module provided by the pim as follow:

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/config/form_extensions/menu.yml
    extensions:
        acme-custom-root-element:                          # The unique key of your extension
            module: pim/menu/tab                           # The module provided by akeneo for root elements
            parent: pim-menu                               # The parent is the root of the menu
            targetZone: mainMenu
            # aclResourceId: my_custom_acl_key             # [optional] You can define a acl check - add this only if the acl has been created
            position: 80                                   # [optional] The position in the tree where you want to add the item
            config:
                title: 'Root'                              # You can define a translation key for the tab name (for example pim_menu.item.import_profile)
                iconModifier: iconCard                     # [optional] The icon of the simple node
                to: pim_importexport_import_profile_index  # The route to redirect to

After running the command ``rm -rf var/cache; bin/console pim:installer:assets; yarn run webpack`` your new item should appear at the root of the menu.

Define a simple node inside an existing tab
*******************************************

If you want to add an element inside an existing sub menu, you can use the item module directly, by changing the parent key of the existing sub menu.
For example, to add new sub menu in system page:

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/config/form_extensions/menu.yml
    extensions:
        acme-custom-system-sub-element:                    # The unique key of your extension
            module: pim/menu/item                          # The module provided by akeneo for sub elements
            parent: pim-menu-system-navigation-block       # The parent key of the existing sub menu
            # aclResourceId: my_custom_acl_key             # [optional] You can define a acl check - add this only if the acl has been created
            position: 240                                  # [optional] The position in the tree where you want to add the item
            config:
                title: 'Sub'                               # You can define a translation key for the item name
                to: pim_importexport_import_profile_index  # The route to redirect

After running the command ``rm -rf var/cache; bin/console pim:installer:assets; yarn run webpack`` your new item should appear in the menu.

Define a simple node inside a custom root menu
**********************************************

For complex application, you may need to create sub menu in a custom root menu. To do this you don't need to have `to` key in the config of the root menu.
By default, by clicking on root menu the user will be redirected to the route of the first item (lowest position)

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/config/form_extensions/menu.yml
    extensions:
        acme-custom-root-element:                          # The unique key of your extension
            module: pim/menu/tab                           # The module provided by akeneo for root elements
            parent: pim-menu                               # The parent is the root of the menu
            targetZone: mainMenu
            # aclResourceId: my_custom_acl_key             # [optional] You can define a acl check - add this only if the acl has been created
            position: 80                                   # [optional] The position in the tree where you want to add the item
            config:
                title: 'Root'                              # You can define a translation key for the tab name (for example pim_menu.item.import_profile)
                iconModifier: iconCard                     # [optional] The icon of the simple node

        acme-custom-root-column:                           # The unique key of your column extension
            module: pim/menu/column                        # The module provided by akeneo for column elements
            parent: pim-menu                               # The parent is the root of the menu
            targetZone: column
            config:
              stateCode: acme-custom-state-code            # The key used on locale storage to know if this menu is collapsed or not
              tab: acme-custom-root-element                # The root menu key we just created

        acme-custom-root-navigation-block:                 # The unique key of your navigation extension
            module: pim/menu/navigation-block              # The module provided by akeneo for navigation elements
            parent: acme-custom-root-column                # The parent is the column we just created
            targetZone: navigation
            config:
                title: 'Root'                              # The label at the top of navigation tab, you can define a translation key

        acme-custom-sub-element:                           # The unique key of your extension
            module: pim/menu/item                          # The module provided by akeneo for sub elements
            parent: acme-custom-root-navigation-block      # The parent is the navigation block we just created
            # aclResourceId: my_custom_acl_key             # [optional] You can define a acl check - add this only if the acl has been created
            position: 120                                  # [optional] The position in the tree where you want to add the item
            config:
               title: 'Sub'                                # You can define a translation key for the item name (for example pim_menu.item.import_profile)
               to: acme_custom_index                       # The route to redirect

The sub menu is only displayed when page reference this sub menu.
If you want to see this new sub menu, you should have a page that reference this sub element (see Highlight menu elements).

Highlight menu elements
***********************

If you want the menu to be highlighted on your custom pages, you have to configure a new form extension in your custom page. The module responsible of highlighting the menu is `pim/common/breadcrumbs`.
This module will both display the breadcrumbs and highlight the menu. You simply have to respectively configure its `tab` and `item` to the menu and sub menu declared above to automatically get the labels and links from the menu.

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/form_extensions/custom_page.yml
    extensions:
        acme-custom-page:
            module: acme/custom_page

        acme-custom-page-breadcrumbs:
            module: pim/common/breadcrumbs
            parent: acme-custom-page
            targetZone: breadcrumbs
            config:
                tab: acme-custom-root-element
                item: acme-custom-sub-element

After running the command ``rm -rf var/cache; bin/console pim:installer:assets; yarn run webpack`` the menu will be highlited when you will open your custom page.

Use you own menu extension item
*******************************

As you may have already guessed, with this system, you can develop your own menu item and add custom information like notification badges or custom display.
