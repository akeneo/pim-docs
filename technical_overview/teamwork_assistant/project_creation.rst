Project creation
================

Overview
--------

Projects are the heart of the Teamwork Assistant. They allow users to know what they have to fill in a selection of
products. In this section, we will understand how a project is created and what are the important steps it goes through
during creation.

.. _project completeness: project_completeness.html

 * project **saved**: Via the UI, the user creates a new project, then saves it. A project is saved when the Project entity has been saved in the database.
 * project **calculated**: Once a project has been saved some background processes are launched. For example, these processes detect impacted users and they compute the `project completeness`_ of each product for multiple user groups
   and permissions using calculation steps.

A project is known as created when these two steps are done.

.. tip::

    Project **created** = Project **saved** + Project **calculated**

Project Format
______________

Once normalized, a project looks like this:

.. code-block:: php

    array:8 => [
      "label" => "Summer collection 2016"
      "code" => "summer-collection-2016"
      "description" => "Our summer collection 2016 is ready for enrichment."
      "due_date" => "2017-01-27"
      "owner" => [] User to [internal_api] format
      "channel" => [] Channel to [internal_api] format,
      "locale" => [] Locale to [internal_api] format,
      "datagridView" => [] DatagridView [internal_api] format
    ]

Calculation Steps
_________________

Once a project is saved, a job is launched in background. The purpose of this job is to compute the completeness and to
notify users concerned by the project.

For each product we need to identify the user groups that have the rights to edit the product, once all the products
have been checked, we send a notification to the concerned users (aka the contributors).

.. _Job: ../import_export/main-concepts.html#job

The job fills in a table that contains user groups that have permissions to edit at least one attribute of a product
that belongs to the project. The job `Job`_ contains steps.
The main step of the job is the `CalculationStep` which is used to execute an action between Project and Products.
The main goal of this step is to extract data from the product to add information to the project.

.. _Item Step: ../import_export/main-concepts.html#step
.. _Add a calculation step: ../../cookbook/teamwork_assistant/calculation_step.html

.. note::

    To get more information about how to add custom item steps in a job go to this `Item Step`_.

.. note::

    To get more information about how to add custom calculation steps during calculation job go to this `Add a calculation step`_.

Project Creation Event
______________________

.. _add notifications: ../../cookbook/teamwork_assistant/customize_notification.html

The PIM offers event on which you can plug listeners to add custom behaviors. For example to `add notifications`_,
or to trigger special actions. This event is located in the class
``Akeneo\TeamworkAssistant\Component\Event\ProjectCreationEvents``.

ProjectCreationEvents::PROJECT_CALCULATED
+++++++++++++++++++++++++++++++++++++++++

This event is dispatched after the end of the job which launches all calculation steps. When this event
is dispatched it ensures that all business around a project is finished.

.. tip::

    This event is used by the Teamwork Assistant to notify users impacted by the project.
