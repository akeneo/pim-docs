How to add a action button or meta data to the product edit form
================================================================

You often need to add a button to the product edit form to perform custom actions. In this cookbook, we will go through each steps needed to achieve this task. Our scenario will be pretty simple: we need to add a button to download our product in csv (like an extra small quick export on the grid). We will assume that we can call a backend action performing this action for us.

Ok ? Let's go !

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
            'pim/form',
            'text!pim/template/product/export-csv',
            'routing'
        ],
        function (
            _,
            BaseForm,
            template,
            Routing
        ) {
            return BaseForm.extend({
                className: 'btn-group',
                template: _.template(template),
                render: function () {
                    this.$el.html(
                        this.template({
                            path: Routing.generate(
                                'acme_csv_product_export',
                                {
                                    id: this.getFormData().meta.id
                                }
                            )
                        })
                    );

                    return this;
                }
            });
        }
    );

With it's attached template:

.. code-block:: html
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/public/templates/product/export-csv.html
    <a class="btn no-hash btn-download" href="<%= path %>">
        <i class="icon-csv"></i>
        <%= _.__('pim_enrich.entity.product.btn.csv_export') %>
    </a>

Now we need to register it in the requirejs configuration:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/requirejs.yml
    require:
        paths:
            pim/product-edit-form/export-csv: pimacme/js/product/form/export-csv

            pim/template/product/export-csv: pimacme/templates/product/export-csv.html


And add it to our product form:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/form_extensions/product_edit.yml

    extensions:
        pim-product-edit-form-export-csv:            # The form extension code (can be whatever you want)
            module: pim/product-edit-form/export-csv # The requirejs module we just created
            parent: pim-product-edit-form            # The parent extension in the form where we want to be registred
            targetZone: buttons
            position: 90                             # The extension position

You can now clear your cache and to admire your brand new button !

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
            'pim/form'
        ],
        function (
            _,
            BaseForm
        ) {
            return BaseForm.extend({
                tagName: 'span',
                template: _.template('<span><%= exportStatus %></span>'),
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

Now, we need to register it in the requirejs configuration:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/requirejs.yml
    require:
        paths:
            pim/product-edit-form/meta/export-status: pimacme/js/product/form/meta/export-status


And add it to our product form:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/form_extensions/product_edit.yml

    extensions:
        pim-product-edit-form-meta/export-status:            # The form extension code (can be whatever you want)
            module: pim/product-edit-form/meta/export-status # The requirejs module we just created
            parent: pim-product-edit-form                    # The parent extension in the form where we want to be registred
            targetZone: meta
            position: 90                                     # The extension position

As always, don't forget to clear your cache to see your new metadata !
