How to Create a New Connector
=============================

We'll implement here a very minimalist Connector, it will do nothing but allow us to understand the main concepts and the overall architecture.

Create our Connector
--------------------

Create a new Symfony bundle:

.. literalinclude:: ../../src/Acme/Bundle/DummyConnectorBundle/AcmeDummyConnectorBundle.php
   :language: php
   :linenos:

Register the bundle in AppKernel:

.. code-block:: php

    public function registerBundles()
    {
        // ...
            new Acme\Bundle\DummyConnectorBundle\AcmeDummyConnectorBundle(),
        // ...
    }

Create our Job
--------------

Create a file ``Resources/config/batch_jobs.yml`` in our Bundle to configure a new job:

.. literalinclude:: ../../src/Acme/Bundle/DummyConnectorBundle/Resources/config/batch_jobs.yml
    :language: yaml
    :linenos:

Here we use an existing dummy reader, a processor and a writer (they implement relevant interfaces and are useable but they do nothing with data).

The reader is implemented in the class ``Pim\Component\Connector\Reader\DummyItemReader``, it's defined as a service in the ConnectorBundle with the alias ``pim_connector.reader.dummy_item`` in the file ``Resources\config\readers.yml``.

The processor is implemented in the class ``Pim\Component\Connector\Processor\DummyItemProcessor``, it's defined as a service in the ConnectorBundle with the alias ``pim_connector.processor.dummy_item`` in the file ``Resources\config\processors.yml``.

The writer is implemented in the class ``Pim\Component\Connector\Writer\DummyItemWriter``, it's defined as a service in the ConnectorBundle with the alias ``pim_connector.writer.dummy_item`` in the file ``Resources\config\writers.yml``.

We'll explain in next cookbook chapters how to create your own elements with real logic inside.

Translate Job and Step titles
-----------------------------

Create a file ``Resources/config/messages.en.yml`` in our Bundle to translate title keys.

.. literalinclude:: ../../src/Acme/Bundle/DummyConnectorBundle/Resources/translations/messages.en.yml
    :language: yaml
    :linenos:

Create a Job Instance
---------------------

Each Job can be configured through a JobInstance, an instance of the Job.

It means we can define a job and several instances of it, with different configurations.

Please note that this job instance does not take any configuration.

We can create an instance with the following command:

.. code-block:: bash

    # akeneo:batch:create-job <connector> <job> <type> <code> <config> [<label>]
    php app/console akeneo:batch:create-job 'Dummy Connector' dummy_job export my_job_instance '[]'

You can also list the existing job instances with the following command:

.. code-block:: bash

    php app/console akeneo:batch:list-jobs

Execute our new Job Instance
----------------------------

You can run the job with the following command:

.. code-block:: bash

    php app/console akeneo:batch:job my_job_instance


.. note::

    This job is not configurable through the PIM UI, we'll see in the next chapters how to write configurable jobs.
