Refresh records completeness
============================

.. note::

   Reference Entities feature is only available for the **Enterprise Edition**.

Usecases
--------

Let's say we have some records having a value for each of those attributes:

- 1 attribute of type Single Option
- 1 attribute of type Multiple Option
- 1 attribute of type Reference entity single link
- 1 attribute of type Reference entity multiple links

Whenever an option is removed from the Option (or Option collection) attribute, the records having the deleted option have to be refreshed in order to correctly compute the completeness.

The same operation needs to happen if a record referenced by another record (through a Reference entity single link or Reference entity multiple links attribute) is deleted.
All the records referencing the deleted record needs to be refreshed.

.. note::

   The record's data coming out of the API will always be accurate, yet filtering on the records' completeness may be de-synchronised.

Command line
------------

To achieve this result, you can use the command:

.. code-block:: bash

    $ php bin/console --env=prod akeneo:reference-entity:refresh-records --all


This command will go over all the records of the database and refresh all the record's values.

It is advised to program a crontab to run at least every day when the users are not using the PIM.

Depending on your reference entities' size (number of records and number of attributes option, option collection, record link and record multiple link) you can run this command multiple times a day.
