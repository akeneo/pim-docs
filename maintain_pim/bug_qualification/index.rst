Bug qualification
=================

Bug qualification is a very important step. The more relevant information you will provide, the more valuable help you will get.

.. warning::

    * If you are an **Enterprise Edition Client**, be aware that the bug qualification is required when you submit a ticket on our `helpdesk <http://helpdesk.akeneo.com/>`_.
    * This step is important as well if you are a **Community Edition user** and if you decide to share your issue on our `Slack user group <https://akeneopim-ug.slack.com/>`_, our `GitHub repository <https://github.com/akeneo/pim-community-dev/>`_, or our `forum <https://www.akeneo.com/forums/>`_.

Prerequisite
------------

.. warning::

    As a prerequisite, make sure you completed all the steps described in our :doc:`/maintain_pim/first_aid_kit/index`.

Frontend bugs
-------------

Frontend bugs are JavaScript or display related issues.

**What to do?**

* Use your browser buit-in developer tools to get more precise error messages and to debug javascript code step by step.
* A frontend error can occur because the backend encountered an error, use the network tab of the developer tools to see if it's the case or not.
* The PIM can send many types of AJAX requests (GET, POST, PUT, DELETE...), make sure the server is configured to accept all of them.

Backend bugs
------------

Backend bugs are issues which occur on the server side (PHP/Symfony code, database...).

**What to do?**

* Use the Symfony buit-in developer tools (the debug toolbar and the profiler).
* Use the Symfony and PIM built-in debug commands (to see them, use the command ``php app/console`` without any arguments).
* Use the tools of your integrated development environment to debug the code step by step.
* Analyze the error messages provided in the Symfony's log files ``/path/to/your/pim/app/logs/prod.log`` or ``/path/to/your/pim/app/logs/dev.log``
* Take a look at the PHP error log file.

.. note::

    If the web debug toolbar is not displayed in ``dev`` environment, you can retrieve the link in ``X-Debug-Token-Link`` header of request.

Tasks bugs
----------

Tasks bugs are issues which occur when running the PIM jobs, like mass edit, import, export... (The way to qualify could be a bit different than pure backend bugs).

**What to do?**

* Analyze the error messages provided in the following file ``/path/to/your/pim/app/logs/batch_execute.log``
* Launch the command out of the PIM with the tools of your integrated development environment ``php app/console akeneo:batch:job <batch_name> <additional_params>``
* Check the to do list of the "Backend bugs" section.

Performances issues
-------------------

When you notice slownesses, displaying pages and/or during import/exports, you are probably experiencing a performance issue.

**What to do?**

* Use the Symfony's profiler timeline tab to identify what part of the execution takes most of the time.
* Use `blackfire.io <https://blackfire.io/>`_ in order to profile the requests which take too much time.
* Use MySQL built in "SHOW PROCESSLIST" query to identify queries which take too long to run (See: `MySQL documentation: SHOW PROCESSLIST Syntax <http://dev.mysql.com/doc/refman/5.6/en/show-processlist.html>`_).
* Use MEMINFO to analyse memory leak issues (See: `MEMINFO GitHub repository <https://github.com/BitOne/php-meminfo/>`_).

Reporting the bug
-----------------

With the information you have gathered, you can now :ref:`Report a bug <report_bug>`.
