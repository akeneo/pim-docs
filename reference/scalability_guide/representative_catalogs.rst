3 representative catalogs
-------------------------

We've audited the application with 3 different catalogs, the data sets are available here https://github.com/akeneo/catalogs/tree/1.4

+------------------------------------+-----------+------------+-------------+
| **Catalog**                        | **Small** | **Medium** | **Large**   |
+------------------------------------+-----------+------------+-------------+
| Products                           | 5000      | 50000      | 2000000     |
+------------------------------------+-----------+------------+-------------+
| Categories                         | 500       | 2000       | 4000        |
+------------------------------------+-----------+------------+-------------+
| Categories / product               | 2         | 2          | 4           |
+------------------------------------+-----------+------------+-------------+
| Attributes                         | 100       | 400        | 5000        |
+------------------------------------+-----------+------------+-------------+
| Attributes Groups                  | 8         | 15         | 20          |
+------------------------------------+-----------+------------+-------------+
| Attributes / Families              | 50        | 100        | 150         |
+------------------------------------+-----------+------------+-------------+
| % filled attributes                | 75%       | 75%        | 75%         |
+------------------------------------+-----------+------------+-------------+
| %localisable attributes            | 10%       | 5%         | 2%          |
+------------------------------------+-----------+------------+-------------+
| %scopable attributes               | 5%        | 2%         | 1%          |
+------------------------------------+-----------+------------+-------------+
| %scopable + localisable attributes | 2%        | 1%         | < 1%        |
+------------------------------------+-----------+------------+-------------+
| Families                           | 20        | 50         | 400         |
+------------------------------------+-----------+------------+-------------+
| Channels                           | 2         | 2          | 4           |
+------------------------------------+-----------+------------+-------------+
| Locales                            | 1         | 4          | 8           |
+------------------------------------+-----------+------------+-------------+

.. note::

    Several of our customers use the PIM with more data on different axes, 10k attributes, 14k categories, etc.
    If it's the case for your project, you'll be interested by other audits an related improvements :doc:`/reference/performances_guide/index`.

Installation
------------

The application is installed on a server following the recommended architecture :doc:`/reference/technical_information/index`.

Depending on the catalog, we use a different database storage:

 * Small catalog is installed with the MySQL storage.
 * Medium catalog is installed with the MySQL storage.
 * Medium catalog is installed with the MongoDB storage.
 * Large catalog is installed with MongoDB storage.

Then, we install the `fixtures` via the installer before to import the products through the default product csv import.

Audit User Interface
--------------------

We use the application in production mode, with xdebug disabled, and we expect an optimal user experience for each page and action.

Audit Backend Processes
-----------------------

We run backend processes (bulk actions, imports, exports, rules execution, etc) in production mode, with xdebug disabled.

With lot of data, processes may run for a quite long time but does not consume more memory than what we advise in :doc:`/reference/technical_information/index`.

Audits Results
--------------

+--------+-------------------+--------------------+
|        | Community Edition | Enterprise Edition |
+--------+-------------------+--------------------+
| Small  | Ok                | WIP                |
+--------+-------------------+--------------------+
| Medium | WIP               | WIP                |
+--------+-------------------+--------------------+
| Large  | WIP               | WIP                |
+--------+-------------------+--------------------+

Known issues
------------

 * WIP
