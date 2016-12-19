Project creation
================

Overview
--------

Projects are the heart of the Activity Manager. They allow users to know what they have to fill on a selection of products.
So in this section, we'll understand how a project is created and what are the important steps it goes through during creation.

.. tip::

    Project **created** = Project **saved** + Project **calculated**

- project **saved**: Through the UI, the user creates a new project, then save it. A project is saved when the Project entity has been saved in database.
- project **calculated**: Once a project has been saved, some background processes are launched to detect impacted users and to compute the completeness of each product for multiple user groups and permissions.

A project is known as created when these 2 steps are done.

Events
------

We offer several events on which you can plug listeners to add custom behaviors, for example to add notifications, or to trigger special actions. All listed events are located in the class ``Akeneo\ActivityManager\Component\Event\ProjectCreationEvents``.

ProjectCreationEvents::PROJECT_SAVED
++++++++++++++++++++++++++++++++++++

This event is dispatched once the project is saved in database.

ProjectCreationEvents::PROJECT_CALCULATED
+++++++++++++++++++++++++++++++++++++++++

This event is dispatched after the end of the job which calculates the product completeness for users impacted by the project.

.. tip::

    This event is used by the Activity Manager to notify users impacted by the project.
