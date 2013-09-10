How to register a new mass edit action on products
==================================================

The Akeneo PIM comes with a number of mass edit actions.
It also comes with a simple way to define your own mass edit action
on selected products.

Creating a MassEditAction
-------------------------
First step is to create a new class that implements ``MassEditAction``:

.. code-block:: php

    namespace Acme\Bundle\DemoBundle\MassEditAction;

    use Pim\Bundle\CatalogBundle\MassEditAction\MassEditAction;

    class CapitalizeValues implements MassEditAction
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

Registering the MassEditAction
------------------------------

Then you must register in the DIC the newly created operation and tag it:

.. configuration-block::

    .. code-block:: yaml

        # src/Acme/Bundle/DemoBundle/Resources/config/services.yml
        services:
            acme_demo_bundle.mass_edit_action.capitalize_values:-
                class: Acme\Bundle\DemoBundle\MassEditAction\CapitalizeValues
                tags:
                    - { name: 'pim_catalog.mass_edit_action', alias: 'capitalize-values' }

    .. code-block:: xml

        <!-- src/Acme/Bundle/DemoBundle/Resources/config/services.xml -->
        <service
            id="acme_demo_bundle.mass_edit_action.capitalize_values"
            class="Acme\Bundle\DemoBundle\MassEditAction\CapitalizeValues">
            <tag name="pim_catalog.mass_edit_action" alias="capitalize-values" />
        </service>

NB: The alias will be used in the url (``/enrich/mass-edit-action/capitalize-values/configure``)

Translating the mass edit action choice
---------------------------------------

Once you've done the previous operations (and eventually cleared your cache), you should see
a new option on the ``/enrich/mass-edit-action/choose`` page.
Akeneo will generate for you a translation key following this pattern:
``pim_catalog.mass_edit_action.%alias%.label``.

You may now define this translation key in your translation catalog(s).
