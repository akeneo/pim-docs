How to add a tab to a form
==========================

The most common UI customization on the Akeneo PIM is to add a tab in your form. With the form extensions introduced in 1.4 we splitted tabs in distinct elements:

- Vertical tabs updates the main content of the page, keeping the same entity context. You can find vertical tabs on the product edit form.
- Horizontal tabs show several information, by staying in the same page. You can find horizontal tabs in a lot of forms (e.g. edit import profile).

In this next image, you can see the difference between a vertical tab and an horizontal tab.

.. image:: ./tabs.png

Add a vertical tab to the product edit form
-------------------------------------------

Let's say that we would like to add a custom tab to our product edit form in order to manage packages of the product.

First, we need to create a form extension in our bundle:

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

The triggered event `tag:register` automatically register a new tab and will add it to the product edit form column.

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
            parent: pim-product-edit-form-column-tabs          # The parent extension in the form where we want to be registered
            targetZone: container                              # The name of the target zone where the element have to be placed
            position: 90                                       # The extension position

After a cache clear (`bin/console cache:clear`), you should see your new tab in the product edit form. If not, make sure that you ran the `bin/console assets:install --symlink web` command.

Now that we have our extension loaded in our form, we can add some logic into it

.. code-block:: javascript
    :linenos:

    'use strict';
    /*
     * /src/Acme/Bundle/EnrichBundle/Resources/public/js/product/form/packages.js
     */
    define(['underscore', 'oro/translator', 'pim/form', 'pim/template/product/tab/packages'],
        function (_, __, BaseForm, template) {
            return BaseForm.extend({
                template: _.template(template),
                configure: function () {
                    this.trigger('tab:register', {
                        code: this.code,
                        isVisible: this.isVisible.bind(this),
                        label: __('pim_enrich.form.product.tab.packages.title')
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
                    return true; // You can define visibility of the tab at runtime with the return of this method
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

Add an horizontal tab
---------------------

Now that we added a tab to the product edit form, adding an horizontal tab will be very easy as it's a quite similar system. For this cookbook we will create a tab to display additional information of an attribute.

Lets start by creating a form extension:

.. code-block:: javascript
    :linenos:

    'use strict';
    /*
     * /src/Acme/Bundle/EnrichBundle/Resources/public/js/attribute/form/tab/additional.js
     */
    define(['jquery', 'underscore', 'oro/translator', 'pim/form', 'pim/template/attribute/tab/additional'],
        function ($, _, __, BaseForm, template) {
            return BaseForm.extend({
                template: _.template(template),
                configure: function () {
                    this.trigger('tab:register', {
                        code: this.code,
                        label: __('pim_enrich.form.attribute.tab.additional.title')
                    });

                    return BaseForm.prototype.configure.apply(this, arguments);
                },
                render: function () {
                    $.getJSON('http://my_wharehouse_api.com/attribute/id')
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

.. code-block:: text
    :linenos:

        # /src/Acme/Bundle/EnrichBundle/Resources/public/templates/attributes/tab/additional.html
        <%= supplyLevel %>

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/requirejs.yml
    config:
        paths:
            pim/attributes/tab/additional: acmeenrich/js/attributes/form/tab/additional

            pim/template/attribute/tab/additional: acmeenrich/templates/attributes/tab/additional.html


.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/form_extensions/attribute/edit.yml

    extensions:
        pim-attribute-edit-form-additional:           # The form extension code (can be whatever you want)
            module: pim/attributes/tab/additional:    # The requirejs module we just created
            parent: pim-attribute-edit-form-form-tabs # The parent extension in the form where we want to be regisetred
            targetZone: container
            position: 90                              # The extension position

To see your changes you need to clear the PIM cache and run webpack again:

.. code-block:: bash

    rm -rf ./var/cache/*
    yarn run webpack
