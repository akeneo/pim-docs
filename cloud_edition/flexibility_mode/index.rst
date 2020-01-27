Flexibility Mode
================

**Flexibility** mode is a Platform as a Service (PaaS) and allows developers to deploy Akeneo PIM and custom code on an Akeneo managed platform.

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

    Major upgrades can require **new version** of software and different components.
    Please contact us through the `Helpdesk > Cloud Flexibility and Serenity <http://helpdesk.akeneo.com/>`_  to request for these specific upgrades and schedule the operation.


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

    docs/backups_management
    docs/disk_usage
