Configuring tasks via crontab
-----------------------------

The application needs the following tasks to be executed in background on a regular basis.

Edit your crontab with ``crontab -e`` and configure your tasks.

.. warning::

    Be sure to update the cron of the user used to install the PIM, to be able to run the command

You have to add these 2 commands to your local installation:

.. code-block:: bash
    :linenos:

    php /path/to/installation/pim-community-standard/bin/console pim:completeness:calculate --env=prod    # recalculates the products completeness
    php /path/to/installation/pim-community-standard/bin/console pim:versioning:refresh --env=prod        # processes pending versions

In the following example, these lines will run the completeness and versioning calculation every 15 minutes:

.. code-block:: bash
    :linenos:

    # m  h  dom  mon  dow  command
    */15 *  *    *    *    php /path/to/installation/pim-community-standard/bin/console pim:completeness:calculate --env=prod > /path/to/installation/pim-community-standard/var/logs/calculate_completeness.log 2>&1
    */15 *  *    *    *    php /path/to/installation/pim-community-standard/bin/console pim:versioning:refresh --env=prod > /path/to/installation/pim-community-standard/var/logs/refresh_versioning.log 2>&1

.. note::

    ``> /path/to/installation/pim-community-standard/var/logs/calculate_completeness.log 2>&1`` is to redirect both stdout and stderr to your log file.

.. warning::

    Since some tasks may take a long time to be executed, adapt the frequency of these tasks according to your needs, to your server capabilities and to your catalog size.
