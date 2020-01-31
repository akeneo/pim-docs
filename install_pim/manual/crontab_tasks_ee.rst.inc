Configuring tasks via crontab
-----------------------------

The application needs the following tasks to be executed in background on a regular basis. The frequency has to be adapted according to your data volume.

Edit your crontab with ``crontab -e`` and configure your tasks.

.. warning::

    Be sure to update the cron of the user used to install the PIM, to be able to run the command

You have to add the following commands to your local installation:

.. code-block:: bash
    :linenos:

    # processes pending versions
    php /path/to/installation/pim-enterprise-standard/bin/console pim:versioning:refresh --env=prod
    # refresh audit data for connections
    php /path/to/installation/pim-enterprise-standard/bin/console akeneo:connectivity-audit:update-data --env=prod
    # executes rules on products
    php /path/to/installation/pim-enterprise-standard/bin/console akeneo:rule:run --env=prod
    # check and send notifications (TeamWork Assistant)
    php /path/to/installation/pim-enterprise-standard/bin/console pimee:project:notify-before-due-date --env=prod
    # check and send notifications for asset date delay
    php /path/to/installation/pim-enterprise-standard/bin/console pim:asset:send-expiration-notification --env=prod
    # recalculate project completeness (TeamWork Assistant)
    php /path/to/installation/pim-enterprise-standard/bin/console pimee:project:recalculate --env=prod
    # re-index all reference entities
    php /path/to/installation/pim-enterprise-standard/bin/console akeneo:reference-entity:refresh-records --all --env=prod
    # recalculates the number of product values
    php /path/to/installation/pim-enterprise-standard/bin/console pim:volume:aggregate --env=prod
    # schedule consolidation and purge of data quality insights related data
    php /path/to/installation/pim-enterprise-standard/bin/console pimee:data-quality-insights:schedule-periodic-tasks --env=prod
    # evaluate all criteria
    php /path/to/installation/pim-enterprise-standard/bin/console pimee:data-quality-insights:evaluate-products --env=prod

    # Franklin Insights specific commands

    # fetch suggested data for products subscribed to Franklin Insights
    php /path/to/installation/pim-enterprise-standard/bin/console pimee:franklin-insights:fetch-products --env=prod
    # push structure and products data in order to compute the Quality Highlights metrics
    php /path/to/installation/pim-enterprise-standard/bin/console pimee:franklin-insights:quality-highlights:push-structure-and-products --env=prod

.. note::
   **Note:** The command ``pimee:data-quality-insights:schedule-periodic-tasks`` has to be scheduled once a day

.. note::
   **Note:** The commands ``pimee:franklin-insights:*`` have to be set only if you activate Franklin Insights EE feature.


.. code-block:: bash
    :linenos:

    # m  h  dom  mon  dow  command
    0 5   *    *    *    php /path/to/installation/pim-enterprise-standard/bin/console pim:versioning:refresh --env=prod > /path/to/installation/pim-enterprise-standard/var/logs/refresh_versioning.log 2>&1
    1 *   *    *    *    php /path/to/installation/pim-enterprise-standard/bin/console akeneo:connectivity-audit:update-data --env=prod > /path/to/installation/pim-enterprise-standard/var/logs/refresh_audit_data.log 2>&1
    0 18  *    *    *    php /path/to/installation/pim-enterprise-standard/bin/console akeneo:rule:run --env=prod > /path/to/installation/pim-enterprise-standard/var/logs/rule_run.log 2>&1
    0 22  *    *    *    php /path/to/installation/pim-enterprise-standard/bin/console pim:volume:aggregate --env=prod > /path/to/installation/pim-enterprise-standard/var/logs/volume_aggregate.log 2>&1

.. note::

    ``> /path/to/installation/pim-enterprise-standard/var/logs/refresh_versioning.log 2>&1`` is to redirect both stdout and stderr to your log file.

.. warning::

    Since some tasks may take a long time to be executed, adapt the frequency of these tasks according to your needs, to your server capabilities and to your catalog size.
