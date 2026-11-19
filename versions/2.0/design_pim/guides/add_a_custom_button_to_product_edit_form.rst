How to add an action button or meta data to the product edit form
=================================================================

In this cookbook, we will go through each step needed to achieve this task. Our scenario will be pretty simple: we need to add a button to download our product in csv (like an extra small quick export on the grid). We will assume that we can call a backend action performing this action for us.

Ok? Let's go!

How to add a button
-------------------

First, we need to create our button:

.. code-block:: javascript
    :linenos:

    'use strict';
    /*
     * /src/Acme/Bundle/EnrichBundle/Resources/public/js/product/form/export-csv.js
     */
    define(
        [
            'underscore',
            'oro/translator',
            'pim/form',
            'pim/template/product/export-csv',
            'routing'
        ],
        function (
            _,
            __,
            BaseForm,
            template,
            Routing
        ) {
            return BaseForm.extend({
                template: _.template(template),
                render: function () {
                    this.$el.html(
                        this.template({
                            path: Routing.generate('acme_csv_product_export', {id: this.getFormData().meta.id}),
                            label: __('pim_enrich.entity.product.btn.csv_export')
                        })
                    );

                    return this;
                }
            });
        }
    );

With its attached template:

.. code-block:: html
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/public/templates/product/export-csv.html
    <a class="AknButton AknButton--apply" href="<%= path %>">
        <%- label %>
    </a>

Now we need to register it in the requirejs configuration:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/requirejs.yml
    config:
        paths:
            pim/product-edit-form/export-csv: acmeenrich/js/product/form/export-csv

            pim/template/product/export-csv: acmeenrich/templates/product/export-csv.html


And add it to our product form:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/form_extensions/product_edit.yml
    extensions:
        pim-product-edit-form-export-csv:            # The form extension code (can be whatever you want)
            module: pim/product-edit-form/export-csv # The requirejs module we just created
            parent: pim-product-edit-form            # The parent extension in the form where we want to be registered
            targetZone: buttons
            position: 90                             # The extension position (lower will be first)

To see your changes you need to clear the PIM cache and run webpack again:

.. code-block:: bash

    rm -rf ./var/cache/*
    yarn run webpack


How to add a meta section
-------------------------

The process of adding a meta information in the product edit form is really similar to adding a button:

As before, we will add our meta section and register it:

.. code-block:: javascript
    :linenos:

    'use strict';
    /*
     * /src/Acme/Bundle/EnrichBundle/Resources/public/js/product/form/meta/export-status.js
     */
    define(
        [
            'underscore',
            'pim/form',
            'pim/template/product/export-status'
        ],
        function (
            _,
            BaseForm,
            template
        ) {
            return BaseForm.extend({
                className: 'AknTitleContainer-metaItem',
                template: _.template(template),
                render: function () {
                    this.$el.html(
                        this.template({
                            //let's asume that export_status is provided by the backend during normalization for example
                            exportStatus: this.getFormData().meta.export_status
                        })
                    );

                    return this;
                }
            });
        }
    );

With its attached template:

.. code-block:: text
    :linenos:

        # /src/Acme/Bundle/EnrichBundle/Resources/public/templates/product/form/meta/export-status.html
        <span title="<%- exportStatus %>">
            <%- exportStatus %>
        </span>

Now, we need to register it in the requirejs configuration:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/requirejs.yml
    config:
        paths:
            pim/product-edit-form/meta/export-status: acmeenrich/js/product/form/meta/export-status

            pim/template/product/export-status: acmeenrich/templates/product/form/meta/export-status.html


And add it to our product form:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/form_extensions/product_edit.yml

    extensions:
        pim-product-edit-form-meta/export-status:            # The form extension code (can be whatever you want)
            module: pim/product-edit-form/meta/export-status # The requirejs module we just created
            parent: pim-product-edit-form                    # The parent extension in the form where we want to be regisetred
            targetZone: meta
            position: 90                                     # The extension position

To see your changes you need to clear the PIM cache and run webpack again:

.. code-block:: bash

    rm -rf ./var/cache/*
    yarn run webpack
