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

PIM updates and migrations
--------------------------

Following the `Semantic Version <https://semver.org>`_, PIM has 2 main updates:

- patches versions:
    Can include **bug fixes**.
    Integrators are accountable for applying new patches and performing migrations on the PIM.
    You can refer to :doc:`../../../migrate_pim/apply_patch/index` for more details about the procedure.
- majors versions:
    Requires **new versions** of software and various components.
    Please contact us through the `Helpdesk > Cloud Flexibility and Serenity <https://akeneo.atlassian.net/servicedesk/customer/portal/8/group/23/create/93?summary=Technological%20stack%20upgrade%20for%20PIM&customfield_13302=12701&customfield_13395=13010&customfield_13395%3A1=13016&description=--%21--%20%20Operation%20scheduling%3A%0A--%21--%20%20%20%20-%202h%20downtime%20expected%0A--%21--%20%20%20%20-%20French%20Office%20Hours%0A--%21--%20%20%20%20-%20Should%20be%20scheduled%2048h%20in%20advance%0A--%21--%20%20Please%20offer%20us%20several%20dates%20and%20we%20will%20confirm%20the%20one%20that%20also%20corresponds%20to%20our%20availability>`_  to schedule the upgrade of the technological stack.
    Then integrators are accountable for performing the PIM migration.
    You can refer to :doc:`../../../migrate_pim/upgrade_major_version` for more details about the procedure.

Quarterly System updates
------------------------

In order for you to benefit the best of Flexibility experience, Akeneo schedules system updates on a quarterly basis.
These updates include **security patches** and **system improvements**.

They will be scheduled every last Wednesday/Thursday of the quarter following these time slots:

- Asia/Australia: Starting **Wednesday at 3pm CET/CEST**
- Europe: Starting **Wednesday at 6pm CET/CEST**
- US: Starting **Thursday at 9am CET/CEST**

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
