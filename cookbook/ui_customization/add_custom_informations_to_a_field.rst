How to add custom informations to a field
=========================================

Natively we add a lot of related informations to product edit fields: data coming from a variant group, validation errors, etc. As an integrator, you can also add custom informations for your own needs and we will go through each steps in this cookbook.

Let's say that we want to display the minimum and maximum values allowed for a number field, for that we need to create a form extension listening on the `pim_enrich:form:field:extension:add` event:

.. code-block:: javascript
    :linenos:

    'use strict';

    define(
        [
            'jquery',
            'underscore',
            'pim/form',
            'oro/mediator'
        ],
        function ($, _, BaseForm, mediator) {
            return BaseForm.extend({
                configure: function () {
                    this.listenTo(mediator, 'pim_enrich:form:field:extension:add', this.addFieldExtension);

                    return BaseForm.prototype.configure.apply(this, arguments);
                },
                addFieldExtension: function (event) {
                    //The event contains the field and an array of promises
                    //You can add your to be sure that the field will wait for you before rendering itself
                    event.promises.push($.Deferred().resolve().then(function () {
                        var field = event.field;

                        if ('pim_catalog_number' === field.attribute.type) {
                            if (!_.isNull(field.attribute.number_min)) {
                                //To add html or a DOM element to a field, you can add the addElement method:
                                // addElement: function (position, code, element)
                                field.addElement(
                                    'footer',
                                    'number_min',
                                    '<span>Min value: ' + field.attribute.number_min + '</span>'
                                );
                            }

                            if (!_.isNull(field.attribute.number_max)) {
                                //To add html or a DOM element to a field, you can add the addElement method:
                                // addElement: function (position, code, element)
                                field.addElement(
                                    'footer',
                                    'number_max',
                                    '<span>Max value: ' + field.attribute.number_max + '</span>'
                                );
                            }

                            //You can also disable the field with the setEditable method of the field
                            //field->setEditable(false);
                        }
                    }.bind(this)).promise());

                    return this;
                }
            });
        }
    );

You can now register it in the `requirejs` configuration file:

.. code-block:: yaml
    :linenos:

    # Acme/Bundle/CustomBundle/Resources/config/requirejs.yml

    config:
        paths:
            acme/product-edit-form/attributes/number-min-max: acmecustom/js/product/form/attributes/number-min-max

And register your extension to the product edit form:

.. code-block:: yaml
    :linenos:

    # Acme/Bundle/CustomBundle/Resources/config/form_extensions.yml

    extensions:
        pim-product-edit-form-number-min-max:
            module: pim/product-edit-form/attributes/number-min-max
            parent: pim-product-edit-form-attributes
            targetZone: self
            position: 100

Don't forget to clear your cache and you are good to go !
