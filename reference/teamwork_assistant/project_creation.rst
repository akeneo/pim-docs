Project creation
================

Overview
--------

Projects are the heart of the Teamwork Assistant. They allow users to know what they have to fill in a selection of
products. In this section, we will understand how a project is created and what are the important steps it goes through
during creation.

.. _calculation steps: calculation_steps.html
.. _project completeness: project_completeness.html

 * project **saved**: Via the UI, the user creates a new project, then saves it. A project is saved when the Project entity has been saved in the database.
 * project **calculated**: Once a project has been saved some background processes are launched. For example, these processes detect impacted users and they compute the `project completeness`_ of each product for multiple user groups
   and permissions using `calculation steps`_.

A project is known as created when these two steps are done.

.. tip::

    Project **created** = Project **saved** + Project **calculated**

Project Event
_____________

.. _add notifications: ../../cookbook/activity_manager/customize_notification.html

The PIM offers event on which you can plug listeners to add custom behaviors. For example to `add notifications`_,
or to trigger special actions. This event is located in the class
``Akeneo\ActivityManager\Component\Event\ProjectCreationEvents``.

ProjectCreationEvents::PROJECT_CALCULATED
+++++++++++++++++++++++++++++++++++++++++

This event is dispatched after the end of the job which launches all `calculation steps`_. When this event
is dispatched it ensures that all business around a project is finished.

.. tip::

    This event is used by the Teamwork Assistant to notify users impacted by the project.
