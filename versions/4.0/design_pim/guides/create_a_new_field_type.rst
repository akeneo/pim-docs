How to add a new field type
===========================

If you want a custom rendering for one of your attributes, attribute types or reference data you need to create a new field type. In this cookbook, we will go through each step needed to create a custom field type.

Let's say we want to have a slider to represent each "number attribute" having a minimum and a maximum value limit.

Before diving into code, we need to understand what's going on under the hood:

- In akeneo, we have attributes with properties and an attribute type. In 1.3 the rendering of an attribute was driven by its attribute type. In 1.4 we introduced a field provider.
- This field provider gets an attribute and returns a field type
- In the `form_extensions.yml` or in the `form_extensions` folder of your `Resources/config` bundle's folder you can map this field to the actual requirejs module
- The requirejs module contains the field's logic


Here is a representation of this architecture:

.. image:: field_process.png

Create a field provider
+++++++++++++++++++++++

To create a custom field, first we need to create a FieldProvider for our new field type:

.. code-block:: php
    :linenos:

    <?php

    namespace Acme\Bundle\CustomBundle\Enrich\Provider\Field;

    use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
    use Akeneo\Platform\Bundle\UIBundle\Provider\Field\FieldProviderInterface;

    class RangeFieldProvider implements FieldProviderInterface
    {
        /**
         * {@inheritdoc}
         */
        public function getField($attribute)
        {
            return 'acme-range-field';
        }

        /**
         * {@inheritdoc}
         */
        public function supports($element)
        {
            //We only support number fields that have a number min and max property
            return $element instanceof AttributeInterface &&
                $element->getType() === 'pim_catalog_number' &&
                null !== $element->getNumberMin() &&
                null !== $element->getNumberMax();
        }
    }


Next, you need to register it in your services.yml file:

.. code-block:: yaml
    :linenos:

    parameters:
        acme.custom.provider.field.range.class: Acme\Bundle\CustomBundle\Enrich\Provider\Field\RangeFieldProvider

    services:
        acme.custom.provider.field.range:
            class: '%acme.custom.provider.field.range.class%'
            tags:
                - { name: pim_enrich.provider.field, priority: 90 }


Your field provider is now registered, congrats!

Create the form field
+++++++++++++++++++++

Now that we have a field provider, we can create the field itself:

.. code-block:: javascript
    :linenos:

    'use strict';

    /*
     * src/Acme/Bundle/CustomBundle/Resources/public/js/product/field/range-field.js
     */
    define([
            'pim/field',
            'underscore',
            'acme/template/product/field/range'
        ], function (
            Field,
            _,
            fieldTemplate
        ) {
            return Field.extend({
                fieldTemplate: _.template(fieldTemplate),
                events: {
                    'change .field-input:first input[type="range"]': 'updateModel'
                },
                renderInput: function (context) {
                    return this.fieldTemplate(context);
                },
                updateModel: function () {
                    var data = this.$('.field-input:first input[type="range"]').val();

                    this.setCurrentValue(data);
                }
            });
        }
    );

And its template:

.. code-block:: html
    :linenos:

    <!-- src/Acme/Bundle/CustomBundle/Resources/public/templates/product/field/range.html -->
    <input
        type="range"
        data-locale="<%= value.locale %>"
        data-scope="<%= value.scope %>"
        value="<%= value.data %>"
        <%= editMode === 'view' ? 'disabled' : '' %>
        min="<%= attribute.number_min %>"
        max="<%= attribute.number_max %>"
    />

You can now register this module into your requirejs configuration:

.. code-block:: yaml
    :linenos:

    # Acme/Bundle/CustomBundle/Resources/config/requirejs.yml

    config:
        paths:
            acme/range-field: acmecustom/js/product/field/range-field

            acme/template/product/field/range: acmecustom/templates/product/field/range.html

After registering this module you must build the frontend with webpack:

.. code-block:: bash

    yarn run webpack

Then, last operation, match the field type (`acme-range-field`) with the requirejs module (`acme/range-field`):

.. code-block:: yaml
    :linenos:

    # Acme/Bundle/CustomBundle/Resources/config/form_extensions.yml

    attribute_fields:
        acme-range-field: acme/range-field

After a cache clear, you can set the min and max value of any number attribute to start to use this new custom field!
