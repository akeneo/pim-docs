Representative data volume
==========================

We've audited the application with different catalogs.

3 representative catalogs
-------------------------

The 3 representative data sets are available here https://github.com/akeneo/catalogs/tree/1.4

+------------------------------------+-------+--------+---------+
| Catalog                            | Small | Medium | Large   |
+------------------------------------+-------+--------+---------+
| Products                           | 5000  | 50000  | 2000000 |
+------------------------------------+-------+--------+---------+
| Categories                         | 500   | 2000   | 4000    |
+------------------------------------+-------+--------+---------+
| Categories / product               | 2     | 2      | 4       |
+------------------------------------+-------+--------+---------+
| Attributes                         | 100   | 400    | 5000    |
+------------------------------------+-------+--------+---------+
| Attributes Groups                  | 8     | 15     | 20      |
+------------------------------------+-------+--------+---------+
| Attributes / Families              | 50    | 100    | 150     |
+------------------------------------+-------+--------+---------+
| % filled attributes                | 75%   | 75%    | 75%     |
+------------------------------------+-------+--------+---------+
| %localisable attributes            | 10%   | 5%     | 2%      |
+------------------------------------+-------+--------+---------+
| %scopable attributes               | 5%    | 2%     | 1%      |
+------------------------------------+-------+--------+---------+
| %scopable + localisable attributes | 2%    | 1%     | < 1%    |
+------------------------------------+-------+--------+---------+
| Families                           | 20    | 50     | 400     |
+------------------------------------+-------+--------+---------+
| Channels                           | 2     | 2      | 4       |
+------------------------------------+-------+--------+---------+
| Locales                            | 1     | 4      | 8       |
+------------------------------------+-------+--------+---------+

Installation
------------

The application is installed on a server following the recommended architecture :doc:`/reference/technical_information/index`.

Depending on the catalog we use a different storage:

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

Processes can run for a quite long time but does not consume more memory than advised in the `technical information` chapter.

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
