How to Create a New Connector
=============================

We'll implement here a very minimalist Connector.

Create our Connector
--------------------

Here, we'll create a new simple connector which uses existing services.

Create a new bundle:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/AcmeDemoConnectorBundle.php
   :language: php
   :linenos:

Register the bundle in AppKernel:

.. code-block:: php
    :linenos:

    public function registerBundles()
    {
        // ...
            new Acme\Bundle\DemoConnectorBundle\AcmeDemoConnectorBundle(),
        // ...
    }

Configure our Job
-----------------

Configure a job in ``Resources/config/batch_jobs.yml``:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/config/batch_jobs.yml
   :language: yaml
   :linenos:
   :lines: 1-13

Here we use some existing readers, processors and writers from native csv product export, they are defined as services in config files of the PimBaseConnectorBundle, we'll see later how to create your own elements.

Title keys can be translated in ``messages.en.yml``

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/translations/messages.en.yml
   :language: yaml
   :linenos:
   :lines: 1-6

Use our new Connector
---------------------

Now if you refresh cache, your new export can be found under Spread > Export profiles > Create export profile.

You can run the job from UI or you can use following command:

.. code-block:: bash

    php app/console akeneo:batch:job my_job_code

Create our Specific Connector
-----------------------------

In the previous section, the main concepts behind connectors were explained.

We have created a new connector which uses existing parts, until we were able to reproduce the native CSV product export features but on a different connector.

Now, let's code a specific connector :doc:`/cookbook/import_export/create-specific-connector`

