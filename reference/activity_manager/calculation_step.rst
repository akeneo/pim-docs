Calculation Steps
=================

Overview
--------

Once a project is saved, a job is launched in background. The purpose of this job is to compute the completeness and to notify users concerned by the project.

For each product we need to identify the user groups that have the rights to edit the product, once all the products have been checked we send a notification
to the concerned users (aka the contributors).

The job fills in a table that contains user groups that have permissions to edit at least one attribute of a
product that belongs to the project. The job `Job <https://docs.akeneo.com/master/reference/import_export/main-concepts.html#job>`_ contains steps.
The main step of the job is the `CalculationStep` which is used to execute an action between Project and Products. The main goal of this step is to
extract data from the product to add information to the project.

.. note::

    To get more information about how to add custom steps go to this `Step <https://docs.akeneo.com/master/reference/import_export/main-concepts.html#step>`_
