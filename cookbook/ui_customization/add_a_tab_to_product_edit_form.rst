How to add a tab or a panel to the product edit form
====================================================

The most common UI customization on the Akeneo PIM is to add a tab to the product edit form. With the new product edit form introduced in 1.4 we splitted tabs in distinct elements:

 - Tabs hold all edit features on the entity (attributes edit, classification, associations)
 - Panels hold all meta information about the entity (history, completeness, comments)

Add a tab to the product edit form
----------------------------------

Let's say that we would like to add a custom tab to our product edit form in order to manage packages of the product.

First, we need to create a Form extension in our bundle:

.. code-block:: javascript
    :linenos:

    'use strict';
    /*
     * /src/Acme/Bundle/EnrichBundle/Resources/public/js/product/form/packages.js
     */
    define(['pim/form'],
        function (BaseForm) {
            return BaseForm.extend({
                configure: function () {
                    this.trigger('tab:register', {
                        code: this.code,
                        isVisible: this.isVisible.bind(this),
                        label: 'Packages tab'
                    });

                    return BaseForm.prototype.configure.apply(this, arguments);
                },
                render: function () {
                    this.$el.html('Hello world');

                    return this;
                },
                isVisible: function () {
                    return true;
                }
            });
        }
    );

For now this is a dummy extension, but this is a good start!

Let's register this file in the `requirejs` configuration

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/requirejs.yml

    config:
        paths:
            pim/product-edit-form/packages: acmeenrich/js/product/form/packages

Now that our file is registered in `requirejs` configuration, we can add this extension to the product edit form:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/form_extensions/product_edit.yml

    extensions:
        pim-product-edit-form-packages:                        # The form extension code (can be whatever you want)
            module: pim/product-edit-form/packages             # The requirejs module we just created
            parent: pim-product-edit-form-form-tabs            # The parent extension in the form where we want to be regisetred
            targetZone: container
            aclResourceId: pim_enrich_product_categories_view  # The user will need this ACL for this extension to be registered
            position: 90                                       # The extension position

After a cache clear (`app/console cache:clear`), you should see your new tab in the product edit form. If not, make sure that you ran the `app/console assets:install --symlink web` command

Now that we have our extension loaded in our form, we can add some logic into it

.. code-block:: javascript
    :linenos:

    'use strict';
    /*
     * /src/Acme/Bundle/EnrichBundle/Resources/public/js/product/form/packages.js
     */
    define(['underscore', 'pim/form', 'text!pim/template/product/tab/packages'],
        function (_, BaseForm, template) {
            return BaseForm.extend({
                template: _.template(template),
                configure: function () {
                    this.trigger('tab:register', {
                        code: this.code,
                        isVisible: this.isVisible.bind(this),
                        label: _.__('pim_enrich.form.product.tab.packages.title')
                    });

                    return BaseForm.prototype.configure.apply(this, arguments);
                },
                render: function () {
                    this.$el.html(this.template({
                        packages: this.getFormData().packages
                    }));

                    return this;
                },
                isVisible: function () {
                    return true; //You can define visibility of the tab at runtime with the return of this method
                }
            });
        }
    );

Remember to register your template in your requirejs file:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/requirejs.yml
    config:
        paths:
            pim/product-edit-form/packages: acmeenrich/js/product/form/packages

            pim/template/product/tab/packages: acmeenrich/templates/product/tab/packages.html

And here is our template to list every package:

.. code-block:: html
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/public/templates/product/tab/packages.html
    <ul>
    <% _.each(packages, function (package) { %>
        <li><%= package.id %></li>
    <% }) %>
    </ul>

Add a panel to the product edit form
------------------------------------

Now that we added a tab to the product edit form, adding a panel will be very easy as it's a quite similar system. For this cookbook we will create a panel to display the supply level of the product in our warehouse.

Lets start by creating a form extension:

.. code-block:: javascript
    :linenos:

    'use strict';
    /*
     * /src/Acme/Bundle/EnrichBundle/Resources/public/js/product/form/panel/warehouse.js
     */
    define(['jquery', 'underscore', 'pim/form', 'text!pim/template/product/panel/warehouse'],
        function ($, _, BaseForm, template) {
            return BaseForm.extend({
                template: _.template(template),
                configure: function () {
                    this.trigger('panel:register', {
                        code: this.code,
                        label: _.__('pim_enrich.form.product.panel.warehouse.title')
                    });

                    return BaseForm.prototype.configure.apply(this, arguments);
                },
                render: function () {
                    $.getJSON('http://my_wharehouse_api.com/product/id')
                        .then(function (supplyLevel) {
                            this.$el.html(this.template({
                                supplyLevel: supplyLevel
                            }));
                        }.bind(this));

                    return this;
                }
            });
        }
    );


Again, we need to register it and create the template:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/requirejs.yml
    config:
        paths:
            pim/product-edit-form/panel/warehouse: acmeenrich/js/product/form/panel/warehouse

            pim/template/product/panel/warehouse: acmeenrich/templates/product/panel/warehouse.html


.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/form_extensions/product_edit.yml

    extensions:
        pim-product-edit-form-warehouse:                  # The form extension code (can be whatever you want)
            module: pim/product-edit-form/panel/warehouse # The requirejs module we just created
            parent: pim-product-edit-form-panels          # The parent extension in the form where we want to be regisetred
            targetZone: panel-content
            position: 90                                  # The extension position

.. code-block:: html
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/public/templates/product/panel/warehouse.html
    <%= supplyLevel %>

Remember to clear your cache and you are good to go!
