Flexibility
================

**Flexibility** mode is a Platform as a Service (PaaS) which allows developers to deploy Akeneo PIM and custom code on an infrastructure managed by Akeneo.

Types of environments
---------------------

.. note::

    Learn how to request for a **new environment** on the `Help Center <https://help.akeneo.com/portal/articles/set-up-akeneo-serenity.html?utm_source=akeneo-docs&utm_campaign=serenity_overview>`_.

**1. Production**

- **my-project**.cloud.akeneo.com
- **can be restored** to an anterior date

**2. Sandbox**

- **my-project-staging**.cloud.akeneo.com
- **no data recovery**

.. note::

    The system components are based on :doc:`../../../install_pim/manual/system_requirements/system_requirements`.

Updates and migrations
----------------------

Updates can include **patches** and **new features**. Integrators are accountables for applying new patches and performing migrations on the PIM.

You can refer to :doc:`../../../migrate_pim/index` for more details about the procedure.

.. note::

    Major upgrades can require **new versions** of software and various components.
    Please contact us through the `Helpdesk > Cloud Flexibility and Serenity <https://akeneo.atlassian.net/servicedesk/customer/portal/8/group/23/create/93?summary=Technological%20stack%20upgrade%20for%20PIM&customfield_13302=12701>`_  to schedule the upgrade of the tech stack.

Backups management
------------------

A snapshot of your production instance is made regularly and can be restored upon request.


Manage your environments
------------------------
.. toctree::
    :maxdepth: 2

    docs/environments_access
    docs/system_administration
    docs/crontasks
    docs/composer_settings
    docs/job_consumers_and_workers

Further readings
----------------

.. toctree::
    :maxdepth: 1

    docs/disk_usage
