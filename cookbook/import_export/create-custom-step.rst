How to Create a Custom Step
===========================

Previously we discussed about :doc:`/cookbook/import_export/create-connector`

Let's see how to go further by creating a custom step which sends a notification to an URL when a product export is finished.

Create our Step
---------------

We will begin by creating a NotifyStep with its configuration and a doExecute method:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Step/NotifyStep.php
   :language: php
   :linenos:
   :lines: 1-4,10-

Create our Step Element
-----------------------

Then we implement a Step Element, in our case, a handler responsible for sending a ping request:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Handler/CurlHandler.php
   :language: php
   :linenos:
   :lines: 1-3,9-

Define the Step Element as Service
----------------------------------

Add your step element to ``services.yml`` to ensure that it is loaded and processed by ``DependencyInjection/AcmeDemoConnectorExtension``:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/config/services.yml
   :language: yaml
   :linenos:

Configure our new Job
---------------------

In ``Resources/config/batch_jobs.yml``, we use a first step to export products in csv and we configure the second one to send a notification:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/config/batch_jobs.yml
   :language: yaml
   :linenos:
   :lines: 1-3,24-

You may have noticed that we use a custom class for the notification step and we define the handler as a step element.

That's it, you can now connect to the PIM and begin to configure and use your new export!
