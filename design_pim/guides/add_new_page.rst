How to add a new page
=====================

In this document, we will see the basics on how to add a new page in Akeneo PIM. We will
- create a new route to this page
- fill this page with some information
- create a link to this one

Create a new route
------------------

First, create a new back-end route to access to your new page

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/routing.yml
    acme_custom_index:
        path: /acme/custom/


Create the page content
-----------------------

Then, you have to create an empty page as a form extension. This will create a simple view with the default template.

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/config/form_extensions/custom-index.yml
    extensions:
        acme-custom-index:
          module: pim/common/simple-view
          config:
              template: pim/template/common/default-template

Create the controller
---------------------

Then, create your controller and register it. This controller will simply render a complete form extension.
This controller will simply load the extension declared above.

.. code-block:: javascript

    // /src/Acme/Bundle/AppBundle/Resources/public/js/controller/custom.js
    'use strict';
    define(['pim/controller/front', 'pim/form-builder'],
        function (BaseController, FormBuilder) {
            return BaseController.extend({
                renderForm: function (route) {
                    return FormBuilder.build('acme-custom-index').then((form) => {
                        form.setElement(this.$el).render();
                    });
                }
            });
        }
    );

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/requirejs.yml
    config:
        paths:
            acme/custom: acmeapp/js/controller/custom

        config:
            pim/controller-registry:
                controllers:
                    acme_custom_index:
                        module: acme/custom

.. note::

    The name of the controller route (`acme_custom_index`) your define on the controller registry has to be the same than the one declared in back-end routes.

Link it
-------

Finally, add a new item in the menu to access your page. You can have more information in :doc:`/design_pim/guides/how_to_customize_menu`.

.. code-block:: yaml

    # /src/Acme/Bundle/AppBundle/Resources/config/form_extensions/menu.yml
    extensions:
        pim-menu-custom:
            module: pim/menu/tab
            parent: pim-menu
            position: 100
            targetZone: mainMenu
            config:
                title: 'Custom'
                iconModifier: iconCard
                to: acme_custom_index

To show your last changes, you have to dump new routes and recompute assets:

.. code-block:: bash

    $ bin/console pim:installer:dump-require-paths
    $ bin/console pim:install:assets
    $ npm run webpack-watch

You will have your new item on the main menu, and when you will on it, it will display an empty page. You can now add
some extensions under `acme-custom-index` to display your custom elements.
