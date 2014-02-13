How to Register a New Mass Edit Action on Products
==================================================

The Akeneo PIM comes with a number of mass edit actions.
It also comes with a simple method to define your own mass edit action
on selected products.

Creating a MassEditAction
-------------------------
The first step is to create a new class that implements ``MassEditActionInterface``:

.. note::
  To avoid rewriting some lines, our example extends AbstractMassEditAction which inherit from MassEditActionInterface.

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/MassEditAction/CapitalizeValues.php
   :language: php
   :prepend: # /src/Acme/Bundle/EnrichBundle/MassEditAction/CapitalizeValues.php
   :linenos:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Form/Type/MassEditAction/CapitalizeValuesType.php
  :language: php
  :prepend: # /src/Acme/Bundle/EnrichBundle/Form/Type/MassEditAction/CapitalizeValuesType.php
  :linenos:


This class will contain all the information about the operation to run and the form type which is used to configure it.


Registering the MassEditAction
------------------------------

After the class is created, you must register it as a service in the DIC with the pim_catalog.mass_edit_action tag:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/services.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/services.yml
   :linenos:


.. note::

    The alias will be used in the url (``/enrich/mass-edit-action/capitalize-values/configure``)

Translating the Mass Edit Action Choice
---------------------------------------

Once you have realized the previous operations (and eventually cleared your cache), you should see
a new option on the ``/enrich/mass-edit-action/choose`` page.
Akeneo will generate for you a translation key following this pattern:
``pim_catalog.mass_edit_action.%alias%.label``.

You may now define this translation key in your translation catalog(s).

