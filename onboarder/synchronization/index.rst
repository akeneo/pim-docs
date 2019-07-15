Synchronization
===============

Push catalog structure to the retailer Onboarder
------------------------------------------------

To work, the retailer Onboarder needs some data from the PIM and you will have to push them once your installation will be done.

Data synchronized are: attribute groups, attributes, attribute option, and families.

.. code-block:: bash

    $ php bin/console akeneo:synchronization:push-catalog-to-onboarder --env=prod


.. warning::

    Please not that the synchronization can take time.


.. note::

    You will have to run this command only once.
    Once the extension is correctly installed, all data mentioned above will be synchronized automatically when they will be saved.


Launch the message worker
-------------------------

| The synchronization of the catalog structure and the catalog data is handled in a asynchronous way using the Google Cloud PubSub service.
| The messages that are part of the synchronization process are queued.
| The queue is consumed by a command line process called ``worker``.
|
| The worker has to always be launched as it polls the queue waiting for new messages to handle.


You want a supervised worker
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You can use supervisor to run the ``worker`` as a daemonized process, supervisor will monitor this process and according to the configuration you'll define it will be autostarted and autorestarted.

.. code-block:: bash

    [program:pim-onboarder-worker]
    command=bin/console akeneo:synchronization:message:consumer --env=prod
    directory=/path/to/your/pim
    user=www-data
    autostart=true
    autorestart=true

.. warning::

    By default the environment variable of the shell that is running the supervisord process will not propagate the environment variables to the process it monitor.
    You have to configure :doc:`the mandatory environment variables </onboarder/installation/index>` that the akeneo/pim-onboarder bundle requires in the ``/etc/supervisor.conf`` file.

.. code-block:: bash

    [supervisord]
    environment=GOOGLE_APPLICATION_CREDENTIALS="/srv/pim/serviceAccount.json",ONBOARDER_TOPIC_NAME_FOR_PUBLICATION_TO_MIDDLEWARE="middleware-topic-name",...

.. note::

    Supervisor documentation: https://github.com/Supervisor/supervisor#documentation

For PAAS (flexibility) customers
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

As you don't have the right to edit or update the supervisor configuraiton, you'll have to add the worker command line in the crontab of the server with the provided wrapper (https://docs.akeneo.com/latest/cloud_edition/flexibility_mode/docs/crontasks.html#shell-wrapper). This wrapper ensure that a command line is launched only one time at the same time (no concurrency).

.. code-block:: bash
    #Custom cron entry for Onboarder worker, launched every 30 minutes
    */30 * * * * akeneo:synchronization:message:consumer --ttl=1700 --env=prod

So for example your complete crontab will look like this:

.. code-block:: bash
    MAILTO="name@domain.tld"
    SHELL="/usr/local/sbin/cron_wrapper.sh"
    #Ansible: akeneo:batch:purge-job-execution
    20 0 1 * * akeneo:batch:purge-job-execution --env=prod
    #Ansible: pim:completeness:calculate
    0 2 * * * pim:completeness:calculate --env=prod
    #Ansible: akeneo:rule:run
    0 5 * * * akeneo:rule:run --env=prod
    #Ansible: pim:versioning:refresh
    30 1 * * * pim:versioning:refresh --env=prod
    #Ansible: pimee:project:notify-before-due-date
    20 0 * * * pimee:project:notify-before-due-date --env=prod
    #Ansible: pim:asset:send-expiration-notification
    0 1 * * * pim:asset:send-expiration-notification --env=prod
    #Ansible: pimee:project:recalculate
    0 2 * * * pimee:project:recalculate --env=prod
    #Ansible: pim:volume:aggregate
    30 4 * * * pim:volume:aggregate --env=prod
    #Ansible: akeneo:batch:publish-job-to-queue franklin_insights_fetch_products
    30 */1 * * * akeneo:batch:publish-job-to-queue franklin_insights_fetch_products --env=prod

    #Custom cron entry for Onboarder worker.
    */30 * * * * akeneo:synchronization:message:consumer --ttl=1700 --env=prod

You want an infinite worker
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Launch the following command line:

.. code-block:: bash

    bin/console akeneo:synchronization:message:consumer --ttl=-1 --env=prod

.. warning::

    This command does not ensure that the worker command line is always started
