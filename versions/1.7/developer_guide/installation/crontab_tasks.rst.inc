Configuring tasks via crontab
-----------------------------

The application needs the following tasks to be executed in background on a regular basis:

.. code-block:: bash
    :linenos:

    # for community and enterprise editions
    /path/to/php /path/to/installation/pim-community-standard/app/console pim:completeness:calculate --env=prod    # recalculates the products completeness
    /path/to/php /path/to/installation/pim-community-standard/app/console pim:versioning:refresh --env=prod        # processes pending versions

    # for enterprise edition only
    path/to/php /path/to/installation/pim-community-standard/app/console akeneo:rule:run --env=prod               # executes rules on products

Edit your crontab with ``crontab -e`` and configure each task. For example, the following line will run the completeness calculation every 15 minutes:

.. code-block:: bash
    :linenos:

    # m  h  dom  mon  dow  command
    */15 *  *    *    *    /path/to/php /path/to/installation/pim-community-standard/app/console pim:completeness:calculate --env=prod > /path/to/installation/pim-community-standard/app/logs/calculate_completeness.log 2>&1

.. note::

    ``> /path/to/installation/pim-community-standard/app/logs/calculate_completeness.log 2>&1`` is to redirect both stdout and stderr to your log file.

.. note::

    Remember that ``dev`` is the default environment. So when you launch a Symfony command, always add ``--env=prod`` in prod environment to avoid useless logging and profiling.

.. warning::

    Since some tasks may take a long time to be executed, adapt the frequency of these tasks according to your needs, to your server capabilities and to your catalog size.