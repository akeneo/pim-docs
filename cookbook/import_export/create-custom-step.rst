How to Create a Custom Notification Step
========================================

The default ItemStep covers the majority of cases but sometimes you need to create more custom logic with no need for a reader, processor or writer.

For instance, at the end of an export you may want to send a custom email, copy the result to a FTP server or call a specific URL to report the result.

Let's see how to go further by creating a custom step which sends a notification to a URL when a product export is finished.

Create our Step
---------------

We will begin by creating a NotifyStep with its configuration and a doExecute method:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Step/NotifyStep.php
   :language: php
   :linenos:

Define the Step as a service
----------------------------

Add your step element to ``steps.yml`` to ensure that it is loaded and processed by ``DependencyInjection/AcmeNotifyConnectorExtension``:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Resources/config/steps.yml
   :language: yaml
   :linenos:

Configure our new Job
---------------------

In ``Resources/config/jobs.yml``, we use a first step to export products in CSV and we configure the second one to send a notification:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Resources/config/jobs.yml
   :language: yaml
   :linenos:

Configure the job parameters
----------------------------

In order to setup the form we use a form configuration provider.

First of all you need to create a new class that will contain the job parameters. This class should implement the following interfaces:
- ``ConstraintCollectionProviderInterface``
- ``DefaultValuesProviderInterface``
- ``FormConfigurationProviderInterface``

Here we decorate the ``ProductCsvExport`` classes in order to retrieve the proper form export configuration.

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/JobParameters/ProductCsvExportNotify.php
   :language: php
   :linenos:
   :lines: 31-39

Then we add the form configuration for our new ``url`` field.

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/JobParameters/ProductCsvExportNotify.php
   :language: php
   :linenos:
   :lines: 56-69

In order to validate the job parameters we need to define some constraints. Here we want a valid url as input.

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/JobParameters/ProductCsvExportNotify.php
   :language: php
   :linenos:
   :lines: 44-51

We also need to add the default values to our job form.

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/JobParameters/ProductCsvExportNotify.php
   :language: php
   :linenos:
   :lines: 74-79

Add the support method with your job name ``csv_product_export_notify``.

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/JobParameters/ProductCsvExportNotify.php
   :language: php
   :linenos:
   :lines: 85-88

Declare this class as a service, with the proper job name as parameter:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Resources/config/job_parameters.yml
   :language: yaml
   :linenos:

Configure the job profile
-------------------------

As it is a job profile based on products export, we need to display the "content" tab on the UI.
To do this, we have to register the view element as visible for our job name:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/AcmeNotifyConnectorBundle.php
   :language: php
   :linenos:

.. note::

   You could also add directly the string of your job name if you have not defined any parameter for it!

Translations
------------

Add a translation for our brand new step:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Resources/translations/messages.en.yml
   :language: yaml
   :linenos:

That's it, you can now connect to the PIM and begin configuring and using your new export!
