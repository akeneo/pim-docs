How to Register a New Mass Edit Action on Products
==================================================

The Akeneo PIM comes with a number of mass edit actions.
It also comes with a simple method to define your own mass edit action
on selected products.


Prerequisite
------------
The mass edit action uses the batch bundle in order to run mass edit in background. Readers and Writers are already
created so in this cookbook we will focus on how to create a Mass Edit Action and create a Processor.
For more information on how to create a Job, Reader, Processor, or Writer please see 'create-specific-connector'


Creating a MassEditAction
-------------------------
The first step is to create a new class in the Operation folder that extends ``AbstractMassEditOperation`` and declare
this new class as a service in the mass_action.yml file

The method ``getBatchJobCode()`` is very important as it determine which job process to use. in our example we will use
the capitalize_values job

You also have to set which item will be used by the mass edit action. In our examples we will mass edit products. So we
have to set 'product' in the ``getItemsName`` method

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/MassEditAction/Operation/CapitalizeValues.php
   :language: php
   :prepend: # /src/Acme/Bundle/EnrichBundle/MassEditAction/Operation/CapitalizeValues.php
   :linenos:

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Form/Type/MassEditAction/CapitalizeValuesType.php
  :language: php
  :prepend: # /src/Acme/Bundle/EnrichBundle/Form/Type/MassEditAction/CapitalizeValuesType.php
  :linenos:


This class will contain all the information about the operation to run and the form type which is used to configure it.


Registering the MassEditAction
------------------------------

After the class is created, you must register it as a service in the DIC with the pim_catalog.mass_edit_action tag:

By default, the operation will be available for the product grid.
It is possible to apply the operation on the family grid though.

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/mass_actions.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/mass_actions.yml
   :linenos:


.. note::

    The alias will be used in the URL (``/enrich/mass-edit-action/capitalize-values/configure``)


Create a new processor
----------------------

In order to capitalize values during the mass edit you have to create a processor that will receive a product.

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Processor/MassEdit/CapitalizeValuesProcessor.php
    :language: php
    :prepend: # /src/Acme/Bundle/EnrichBundle/MassEditAction/MassEdit/CapitalizeValuesProcessor.php
    :linenos:


Register the Processor
----------------------

After the class is created, you must register it as a service in the DI

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/processors.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/processors.yml
    :linenos:


Create a new Job
----------------

As the mass edit use the batch bundle you have to use a Reader a Processor and a Writer

.. literalinclude:: ../../src/Acme/Bundle/EnrichBundle/Resources/config/batch_jobs.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/config/batch_jobs.yml
    :linenos:


Add the new job to the fixtures
-------------------------------

Jobs are created

.. literalinclude:: ../../src/Acme/Bundle/InstallBundle/Resources/fixtures/icecat_demo_dev/jobs.yml
    :language: yaml
    :prepend: # /src/Acme/Bundle/EnrichBundle/Resources/fixtures/icecat_demo_dev/jobs.yml
    :linenos:


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
