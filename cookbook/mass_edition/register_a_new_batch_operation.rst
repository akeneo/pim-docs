How to register a new batch operation on products
=================================================

The Akeneo PIM comes with a number of batch operations.
It also comes with a simple way to define your own batch operation
on selected products.

Creating a BatchOperation
------------------------
First step is to create a new class that implements ``BatchOperation``:

.. code-block:: php

    namespace Acme\Bundle\DemoBundle\BatchOperation;

    use Pim\Bundle\CatalogBundle\BatchOperation\BatchOperation;

    class CapitalizeValues implements BatchOperation
    {
        protected $attributeNames;

        public function getFormType()
        {
            return new CapitalizeValuesType();
        }

        public function perform(array $products)
        {
            foreach ($products as $product) {
                foreach ($product->getValues() as $value) {
                    if (in_array($value->getAttribute()->getCode(), $this->attributeNames)) {
                        $value->setData(ucfirst($value->getData()));
                    }
                }
            }
        }
    }

This class is the one that will store all the information about the operation to run and
the form type to display to configure it.

Registering the BatchOperation
------------------------------

Then you must register in the DIC the newly created operation and tag it:

.. code-block:: yaml

    # src/Acme/Bundle/DemoBundle/Resources/config/services.yml
    services:
        acme_demo_bundle.batch_operation.capitalize_values:
            class: Acme\Bundle\DemoBundle\BatchOperation\CapitalizeValues
            tags:
                - { name: 'pim_catalog.batch_operation', alias: 'capitalize-values' }

.. code-block:: xml

    <!-- src/Acme/Bundle/DemoBundle/Resources/config/services.xml -->
    <service
        id="acme_demo_bundle.batch_operation.capitalize_values"
        class="Acme\Bundle\DemoBundle\BatchOperation\CapitalizeValues">
        <tag name="pim_catalog.batch_operation" alias="capitalize-values" />
    </service>

NB: The alias will be used in the url (``/enrich/batch-operation/capitalize-values/configure``)

Translating the batch operation choice
--------------------------------------

Once you've done the previous operations (and eventually cleared your cache), you should see
a new option on the ``/enrich/batch-operation/choose`` page.
Akeneo will generate for you a translation key following this pattern:
``pim_catalog.batch_operation.%alias%.label``.

You may now define this translation key in your translation catalog(s).
