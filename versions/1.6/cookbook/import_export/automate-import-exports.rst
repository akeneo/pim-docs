Automate imports/exports
========================

A common need when it comes to imports/exports is to be able to automate them. As Akeneo PIM provides Symfony commands to manage imports and exports you can easily schedule them.

The batch job command
---------------------

Akeneo PIM provides a simple command to launch jobs:

.. code-block:: bash
    :linenos:

    app/console akeneo:batch:job [-c|--config CONFIG] [--email EMAIL] [--no-log] [--] <code>

So to run the job csv_product_import you can run:

.. code-block:: bash
    :linenos:

    app/console akeneo:batch:job csv_product_import --env=prod

.. tip::
    Don't forget to add --env=prod to avoid memory leaks in dev environment (the default environment for commands)

You can also provide a custom configuration (in JSON format) for the job:

.. code-block:: bash
    :linenos:

    app/console akeneo:batch:job csv_product_import -c "{\"filePath\": \"/custom/path/to/product.csv\"}" --env=prod

Scheduling the jobs
-------------------

To run a command periodically, you can use a cron_:

.. _cron: https://help.ubuntu.com/community/CronHowto

First, you need to install it (example in debian/ubuntu based distributions):

.. code-block:: bash
    :linenos:

    apt-get install cron

Then, you can edit your crontab:

.. code-block:: bash
    :linenos:

    crontab -e

You can now add a new line at the end of the opened file:

.. code-block:: bash
    :linenos:

    0 * * * * /home/akeneo/pim/app/console akeneo:batch:job csv_product_import -c "{\"filePath\": \"/custom/path/to/product.csv\"}" --env=prod > /tmp/import.log

With this cron configuration a product import will be launched every hour with the file `/custom/path/to/product.csv`
