How to Register a New Mass Edit Action on Products
==================================================

The Akeneo PIM comes with a number of mass edit actions.
It also comes with a simple method to define your own mass edit action
on selected products.

Creating a MassEditAction
-------------------------
The first step is to create a new class that implements ``MassEditAction``:

.. code-block:: php
    :linenos:

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

This class will contain all the information about the operation to run and the form type which is used to configure it.


Registering the MassEditAction
------------------------------

After the class is created, you must register it as a service in the DIC with the pim_catalog.mass_edit_action tag:

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

.. note::

    The alias will be used in the url (``/enrich/mass-edit-action/capitalize-values/configure``)

Translating the Mass Edit Action Choice
---------------------------------------

Once you have realized the previous operations (and eventually cleared your cache), you should see
a new option on the ``/enrich/mass-edit-action/choose`` page.
Akeneo will generate for you a translation key following this pattern:
``pim_catalog.mass_edit_action.%alias%.label``.

You may now define this translation key in your translation catalog(s).
