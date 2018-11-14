Create a custom product export builder filter
=============================================

When you create a new attribute type or if you want to change the rendering of a filter you need to create a custom filter. In this quick cookbook we will go through all the steps to achieve this task.

Declare a filter provider for our custom filter
+++++++++++++++++++++++++++++++++++++++++++++++

To create a custom filter, first we need to create a FilterProvider for our new filter type:

.. code-block:: php
    :linenos:

    <?php

    namespace Acme\Bundle\CustomBundle\Enrich\Provider\Filter;

    use Akeneo\Platform\Bundle\UIBundle\Provider\Filter\FilterProviderInterface;
    use Akeneo\Pim\Structure\Component\Model\AttributeInterface;

    class RangeFilterProvider implements FilterProviderInterface
    {
        /**
         * {@inheritdoc}
         */
        public function getFilters($attribute)
        {
            // We expose our future custom filter for the product export builder
            return ['product-export-builder' => 'acme-range-filter'];
        }

        /**
         * {@inheritdoc}
         */
        public function supports($element)
        {
            return $element instanceof AttributeInterface  &&
                $element->getAttributeType() === 'pim_catalog_number' &&
                null !== $element->getNumberMin() &&
                null !== $element->getNumberMax();
        }
    }

Now we can register this filter provider in our service.yml file

.. code-block:: yaml
    :linenos:

    services:
        acme.custom.provider.filter.range:
            class: Acme\Bundle\CustomBundle\Enrich\Provider\Filter\RangeFilterProvider
            tags:
                - { name: pim_enrich.provider.filter, priority: 110 }

Your filter provider is now registered, congrats!

Create the filter
+++++++++++++++++

Now that we have a filter provider, we can create the filter itself:

.. code-block:: javascript
    :linenos:

    'use strict';
    /*
     * src/Acme/Bundle/CustomBundle/Resources/public/js/product/filter/range-filter.js
     */
    define([
        'underscore',
        'oro/translator',
        'pim/filter/attribute/attribute',
        'acme/template/product/filter/range',
        'jquery.select2'
    ], function (
        _,
        __,
        AttributeFilter,
        template
    ) {
        return AttributeFilter.extend({
            shortname: 'range',
            template: _.template(template),

            /*
            We listen on the change event of the range field.
             */
            events: {
                'change [name="filter-value"]': 'updateState'
            },

            configure: function () {
                this.listenTo(this.getRoot(), 'pim_enrich:form:entity:pre_update', function (data) {
                    // Before the set data, we define the defaults values of our field
                    _.defaults(data, {field: this.getCode(), value: '', operator: '>='});
                }.bind(this));

                return AttributeFilter.prototype.configure.apply(this, arguments);
            },

            renderInput: function (templateContext) {
                // It's time to render our field
                return this.template(_.extend({}, templateContext, {
                    value: this.getValue()
                }));
            },

            updateState: function () {
                // When the dom change, we update our internal model
                this.setData({
                    field: this.getField(),
                    operator: this.getOperator(),
                    value: this.$('[name="filter-value"]').val()
                });
            }
        });
    });

And its template:

.. code-block:: html
    :linenos:

    <!-- src/Acme/Bundle/CustomBundle/Resources/public/templates/product/filter/range.html -->
    <input
        type="range"
        name="filter-value"
        value="<%= value %>"
        min="<%= attribute.number_min %>"
        max="<%= attribute.number_max %>"
        <%- editable ? '' : 'disabled' %>
    />

You can now register this module into our requirejs configuration:

.. code-block:: yaml
    :linenos:

    # Acme/Bundle/CustomBundle/Resources/config/requirejs.yml
    config:
        paths:
            acme/range-filter: acmecustom/js/product/filter/range-filter

            acme/template/product/filter/range: acmecustom/templates/product/filter/range.html

After registering this module you must build the frontend with webpack:

.. code-block:: bash

    yarn run webpack

Then, last operation, match the filter type (`acme-range-filter`) with the requirejs module (`acme/range-filter`):

.. code-block:: yaml
    :linenos:

    # Acme/Bundle/CustomBundle/Resources/config/form_extensions.yml
    extensions:
        acme-range-filter:
            module: acme/range-filter

After a cache clearing, we can now set the min and max value of any number attribute to start to use this new custom filter!
