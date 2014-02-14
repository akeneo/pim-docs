How to Register a New Mass Edit Action on Products
==================================================

The Akeneo PIM comes with a number of mass edit actions.
It also comes with a simple method to define your own mass edit action
on selected products.

Creating a MassEditAction
-------------------------
The first step is to create a new class that implements ``MassEditActionInterface``:

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


Templating the form of Mass Edit Action
---------------------------------------

You need to create a template to render your Mass Edit Action form.

.. note::

  The template must be in ``/src/Acme/Bundle/EnrichBundle/Resources/views/MassEditAction/configure/``

.. literalinclude:: 
   ../../src/Acme/Bundle/EnrichBundle/Resources/views/MassEditAction/configure/capitalize-values.html.twig
   :language: jinja
   :prepend: #  /src/Acme/Bundle/EnrichBundle/Resources/views/MassEditAction/configure/capitalize-values.html.twig
   :linenos:


Translating the Mass Edit Action Choice
---------------------------------------

Once you have realized the previous operations (and eventually cleared your cache), you should see
a new option on the ``/enrich/mass-edit-action/choose`` page.
Akeneo will generate for you a translation key following this pattern:
``pim_catalog.mass_edit_action.%alias%.label``.

You may now define some translation keys (``label, description and success_flash``) in your translation catalog(s).

