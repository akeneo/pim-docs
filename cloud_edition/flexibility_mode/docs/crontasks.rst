Crontasks settings
==================

You'll find here some info about crontasks management in Akeneo PIM Flexibility Edition:

1. Default provided CRON tasks use a custom wrapper script defined by *SHELL* that calls the PIM `bin/console` command.

   It avoids us to write on each cron line the full script path to call and to redirect logs.

   Adding your own cron line fails because it tries to run your command with our wrapper.
   
   To normally run your own scripts (without our wrapper), you just have to redefine the shell variable like this `SHELL=/bin/bash` before your tasks
    
2. We would like to remind you that all servers use UTC time, don't forget to convert you desired local time to UTC time.

.. code-block:: bash

    # ┌───────────── minute (0 - 59)
    # │ ┌───────────── hour (0 - 23)
    # │ │ ┌───────────── day of month (1 - 31)
    # │ │ │ ┌───────────── month (1 - 12)
    # │ │ │ │ ┌───────────── day of week (0 - 6) (Sunday to Saturday;
    # │ │ │ │ │                                       7 is also Sunday on some systems)
    # │ │ │ │ │
    # │ │ │ │ │
    # * * * * *  command to execute


4. To be notified about CRONs executions (errors only), `MAILTO` variable should be changed and set with someone concerned in your company.
   i.e.: `MAILTO="projectmanager@acme.com"`


You can find below a full example of the crontab that you can use:

.. code-block:: bash

    SHELL=“/usr/local/sbin/cron_wrapper.sh”
    MAILTO="projectmanager@acme.com"
    #Ansible: pim:versioning:refresh
    30 1 * * * pim:versioning:refresh --env=prod
    #Ansible: akeneo:rule:run
    15 * * * * akeneo:rule:run --env=prod
    #Ansible: pim:completeness:calculate
    45 */2* * * pim:completeness:calculate --env=prod
    #Ansible: akeneo:batch:purge-job-execution
    20 0 1 * * akeneo:batch:purge-job-execution --env=prod

    # My custom jobs
    SHELL=/bin/bash

    0 2 * * * sh /home/akeneo/bin/mysscript.sh
    15 2 * * * python /home/akeneo/bin/myexport.py


