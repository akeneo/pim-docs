Ensure the Onboarder worker is always running
=============================================

You have to use Cron to ensure the worker is always running. For that, run the following command to edit the crontab:

.. code-block:: bash

   crontab -e

Then add the following line at the end of the crontab:

.. code-block:: bash

   42 */2 * * * akeneo:synchronization:message:consumer --env=prod --retry-limit=5 --ttl=6900
