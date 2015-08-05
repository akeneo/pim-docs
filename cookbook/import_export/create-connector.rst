How to Create a New Connector
=============================

We'll implement here a very minimalist Connector.

Create our Connector
--------------------

Here, we'll create a new simple connector which uses existing services.

Create a new Symfony bundle:

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

Create our Job
--------------

Create a file ``Resources/config/batch_jobs.yml`` in our Bundle to configure a new job:

.. code-block:: yaml

    connector:
        name: Demo Connector
        jobs:
            demo_job:
                title: acme_connector.jobs.demo_job.title
                type:  export
                steps:
                    stepOne:
                        title:         acme_connector.jobs.demo_job.export.title
                        services:
                            reader:    pim_connector.reader.dummy_item
                            processor: pim_connector.processor.dummy_item
                            writer:    pim_connector.writer.dummy_item

Here we use some existing dummy reader, processor and writer.

Nothing will happen with this job but it's a good way to discover different concepts.

The reader is implemented in the class ``Pim\Component\Connector\Reader\DummyItemReader``, it's defined as a service in the ConnectorBundle with the alias ``pim_connector.reader.dummy_item`` in the file ``Resources\config\readers.yml``.

The processor is implemented in the class ``Pim\Component\Connector\Processor\DummyItemProcessor``, it's defined as a service in the ConnectorBundle with the alias ``pim_connector.processor.dummy_item`` in the file ``Resources\config\processors.yml``.

The writer is implemented in the class ``Pim\Component\Connector\Writer\DummyItemWriter``, it's defined as a service in the ConnectorBundle with the alias ``pim_connector.writer.dummy_item`` in the file ``Resources\config\writers.yml``.

We'll see in next cookbook chapters on how to create your own elements.

Translate Job and Step titles
-----------------------------

Create a file ``Resources/config/messages.en.yml`` in our Bundle to translate title keys.

.. code-block:: yaml

    acme_connector:
        jobs:
            demo_job:
                title: Demo Job
                stepOne:
                    title: First Step

Create a Job Instance
---------------------

Each Job can be configured through a JobInstance, an instance of the Job.

Means that we can define a job and many instances of this one, with different configurations.

Our demo job does not take any configuration, we can create an instance with the following command:

.. code-block:: bash

    # akeneo:batch:create-job <connector> <job> <type> <code> <config> [<label>]
    php app/console akeneo:batch:create-job 'Demo Connector' demo_job export myJobInstance '[]'

You can also list the existing job instance with the following command:

.. code-block:: bash

    php app/console akeneo:batch:list-jobs

Execute our new Job Instance
----------------------------

You can run the job with the following command:

.. code-block:: bash

    php app/console akeneo:batch:job myJobInstance

.. note::

    This job is not configurable and runnable from the UI (it doesn't contain expected configuration).
