How to customize the Back Office menu
=====================================

The Akeneo back office uses the Oro Platform Application Menu.

The NavigationBundle automatically processes a YAML configuration file which must be named ``navigation.yml`` and located in ``Resources/config`` directory of the registered bundle.

The configuration is placed under ``oro_menu_config`` tree.

Menu items creation
*******************

New entries are created under the ``items`` key. Each item must have a ``label`` and a ``route`` (or an ``uri``).

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/navigation.yml
    oro_menu_config:
        items:
            reference_data:
                label: 'Reference Data'
                uri: '#'
            brand:
                label: 'Brands'
                route: 'app_brands_index'
            manufacturer:
                label: 'Manufacturers'
                route: 'app_manufacturers_index'

The example above defines three menu items:
 - The ``reference_data`` item consists of a label and the URI ``#``. This means that the item will not react on mouse click,
   but can be used as a placeholder for nested menus;
 - Both ``brand`` and ``manufacturer`` items reference and existing route. So, when we will click one of theses items, we will
   get the corresponding page.

Here is the list of all available options for an item:

.. code-block:: yaml

    items: #menu items
        <key>: # menu item identifier. used as default value for name, route and label, if it not set in options
            aclResourceId                     # ACL resource Id
            translateDomain: <domain_name>    # translation domain
                translateParameters:          # translation parameters
            label: <label>                    # label text or translation string template
            name:  <name>                     # name of menu item, used as default for route
            uri: <uri_string>                 # uri string, if no route parameter set
            route: <route_name>               # route name for uri generation, if not set and uri not set - loads from key
                routeParameters:              # router parameters
            attributes: <attr_list>           # <li> item attributes
            linkAttributes: <attr_list>       # <a> anchor attributes
            labelAttributes: <attr_list>      # <span> attributes for text items without link
            childrenAttributes: <attr_list>   # <ul> item attributes for nested lists
            showNonAuthorized: <boolean>      # show for non-authorized users
            display: <boolean>                # disable showing of menu item
            displayChildren: <boolean>        # disable showing of menu item children

Organize the Navigation trees
*****************************

The next step is to put together a tree for these menu items All trees are built under the ``tree`` key.

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/navigation.yml
    oro_menu_config:
        items: # ...
        tree:
            application_menu:
                children:
                    system_tab:
                        children:
                            reference_data:
                                children:
                                    brand: ~
                                    manufacturer: ~
            usermenu:
                children:
                    brand: ~

Akeneo PIM is provided with two trees where items can be added.

 - ``application_menu``: the horizontal main menu on top of the back office;
 - ``usermenu``: the menu that pops up when the user clicks on their username in the top right corner of the screen.

In the example above, items are also registered in an already existing subtree *System* tab. With the given configuration,
the menu *Reference Data* will appear under the existing *System* tab of the application menu.
