Setting up the job scheduling
===============================

Purpose of the cron
--------------------

Since version 7.0, the user can schedule a job directly on the PIM. It can also define the user used to executed the job.
In order to have this job launched at the right moment you should launch periodically a command that will check if there is a scheduled job to launch.

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

    */10 * * * * /home/akeneo/pim/bin/console pim:job-automation:push-scheduled-jobs-to-queue

With this cron configuration the application will pushed into the job queue every ten minutes the job that need to be launched according to the user configuration

.. warning::

    One daemon or several daemon processes have to be started to execute the jobs.
    Please follow the documentation :doc:`/install_pim/manual/daemon_queue` if it's not the case.
