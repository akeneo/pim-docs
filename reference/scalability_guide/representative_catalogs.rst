Audit with 3 Representative Catalogs
====================================

.. warning::

    This is an early version of this page, we'll continue to complete it with more use cases.

We've audited the application with 3 different representative catalogs:

+-----------------------------------------+-----------+------------+-------------+
| **Catalog**                             | **Small** | **Medium** | **Large**   |
+-----------------------------------------+-----------+------------+-------------+
| Products                                | 5.000     | 50.000     | 2.000.000   |
+-----------------------------------------+-----------+------------+-------------+
| Categories                              | 500       | 2.000      | 4.000       |
+-----------------------------------------+-----------+------------+-------------+
| Categories / product                    | 2         | 2          | 4           |
+-----------------------------------------+-----------+------------+-------------+
| Attributes                              | 100       | 400        | 1.000       |
+-----------------------------------------+-----------+------------+-------------+
| Attributes Groups                       | 8         | 15         | 20          |
+-----------------------------------------+-----------+------------+-------------+
| Attributes / Families                   | 50        | 100        | **WIP**     |
+-----------------------------------------+-----------+------------+-------------+
| % filled attributes                     | 75%       | 75%        | 75%         |
+-----------------------------------------+-----------+------------+-------------+
| %localisable attributes                 | 10%       | 5%         | 2%          |
+-----------------------------------------+-----------+------------+-------------+
| %scopable attributes                    | 5%        | 2%         | 1%          |
+-----------------------------------------+-----------+------------+-------------+
| %scopable + localisable attributes      | 2%        | 1%         | < 1%        |
+-----------------------------------------+-----------+------------+-------------+
| Families                                | 20        | 50         | 400         |
+-----------------------------------------+-----------+------------+-------------+
| Channels                                | 2         | 2          | **WIP**     |
+-----------------------------------------+-----------+------------+-------------+
| Enabled Locales                         | 1         | 4          | **WIP**     |
+-----------------------------------------+-----------+------------+-------------+
| **Audit Status for Community Edition**  | **DONE**  | **DONE**   | **WIP**     |
+-----------------------------------------+-----------+------------+-------------+
| **Audit Status for Enterprise Edition** | **WIP**   | **WIP**    | **WIP**     |
+-----------------------------------------+-----------+------------+-------------+

These catalogs are available here https://github.com/akeneo/catalogs/tree/1.4.

You can also use our data generator https://github.com/akeneo-labs/DataGeneratorBundle.

Installation
------------

The application is installed on a server following the recommended architecture :doc:`/reference/technical_information/index`.

Depending on the catalog, we use a different database storage. We install the data `fixtures` via the installer before to import the products through the default product csv import.

+---------+---------+----------------+
|         | Storage | Product values |
+---------+---------+----------------+
| Small   | MySQL   | 159.676        |
+---------+---------+----------------+
| Medium  | MySQL   | 3.661.981      |
+---------+---------+----------------+
| Large   | MongoDB | **WIP**        |
+---------+---------+----------------+

.. note::

    If you want to know more about how we choose the relevant storage, please read :doc:`/reference/scalability_guide/more_than_5M_product_values`

Audit User Interface
--------------------

We use the application in production mode, with xdebug disabled, and we expect an optimal user experience for each page and action.

Audit Backend Processes
-----------------------

We run backend processes (bulk actions, imports, exports, rules execution, etc) in production mode, with xdebug disabled. Depending on the amount of data, processes may run for quite a long time but does not consume more memory than what we advise in :doc:`/reference/technical_information/index`. Please note that for some project data set, several extra configurations are required (for instance, change the size of a bulk of products for the rules execution).

Known limitations on representative catalogs
--------------------------------------------

 - Memory leaks (when a process consumes more memory than recommended) are qualified as bugs and are released in patches versions.
 - Scalability limitations (when we try to support larger data volume for an axis) are qualified as improvements and are released in minor versions.

+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| **Type** | **Catalog** | **Edition** | **Released** | **Note**                                                                                                                                                       |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| memleak  | All         | All         | WIP          | (PIM-5507) Memory leak when executing mass edit or mass publish on several thousands of products                                                               |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | All         | All         | v1.4.19      | (PIM-5476) Creation of useless empty product values when importing new products (may divide by 2 the exec time)                                                |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | Medium      | All         |              | (PIM-5467) First load of the completeness widget is too long (ORM)                                                                                             |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | Large       | All         |              | (PIM-5518) Timeout with synchronous update of products when remove 'AssociationType', 'Attribute', 'AttributeOption', 'Category', 'Family', 'Group', 'Channel' |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | Large       | All         |              | (PIM-xxxx) MongoDB timeout when filter and sort by date, on product grid (cf Elastic Search Bundle)                                                            |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+

Examples of customers instance
------------------------------

Several customers challenge the limitations in custom projects, it sometimes requires dedicated optimizations. We continuously improve the product scalability in each minor version and we are always interested by new use cases to cover. Don't hesitate to contact us if you need help to scale your instance.

**On standard axes:**

+-----------------------------------------+----------------+----------------+----------------+----------------+--------------------------------------------------------------+
| **Catalog**                             | **Customer 1** | **Customer 2** | **Customer 3** | **Customer 4** | **Details about limitations**                                |
+-----------------------------------------+----------------+----------------+----------------+----------------+--------------------------------------------------------------+
| Products                                | **2.000.000**  | 1.000.000      | 80.000         | 10.000         |                                                              |
+-----------------------------------------+----------------+----------------+----------------+----------------+--------------------------------------------------------------+
| Products values                         | **43.398.847** | **78.606.501** | ?              | ?              | These customers use Elastic Search Bundle                    |
+-----------------------------------------+----------------+----------------+----------------+----------------+--------------------------------------------------------------+
| Attributes                              | 1.800          | **8.000**      | 240            | 355            | :doc:`/reference/scalability_guide/more_than_10k_attributes` |
+-----------------------------------------+----------------+----------------+----------------+----------------+--------------------------------------------------------------+
| Families                                | 131            | **3.500**      | 44             | 3              | :doc:`/reference/scalability_guide/more_than_10k_families`   |
+-----------------------------------------+----------------+----------------+----------------+----------------+--------------------------------------------------------------+
| Categories                              | 2613           | **14.000**     | 740            | 60             | :doc:`/reference/scalability_guide/more_than_10k_categories` |
+-----------------------------------------+----------------+----------------+----------------+----------------+--------------------------------------------------------------+
| Channels                                | 1              | 2              | 2              | **14**         |                                                              |
+-----------------------------------------+----------------+----------------+----------------+----------------+--------------------------------------------------------------+
| Enabled Locales                         | 1              | 1              | **36**         | 1              |                                                              |
+-----------------------------------------+----------------+----------------+----------------+----------------+--------------------------------------------------------------+

**On other axes or combinations:**

+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
|                                    | **Tested** | **In custom project** | **Details about limitations**                                           |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Attribute options                  | 95.000     |                       |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Attribute options per attribute    | 500        |                       |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Reference data                     | [WIP]      |                       |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Reference data per attribute       | [WIP]      |                       |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Product groups                     | 10.000     |                       | cf following PIM-5519, PIM-5363                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Products per product group         | 50         |                       |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Product variant groups             | 10.000     |                       | cf following PIM-5467, PIM-5520, PIM-5363                               |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Products per product variant group | 50         |                       |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Product values per variant group   | 50         |                       |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Product associations               | [WIP]      |                       | cf following PIM-5363                                                   |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Attributes per family              | 150        |                       |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Attributes per attribute group     | 150        | 1.500                 |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Product values per product         | 200        |                       |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Rules                              | 150        | 3.000                 | :doc:`/reference/performances_guide/rules_execution_memory_usage`       |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Product assets                     | [WIP]      |                       |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+
| Product drafts                     | [WIP]      |                       |                                                                         |
+------------------------------------+------------+-----------------------+-------------------------------------------------------------------------+

Other known limitations [WIP]
-----------------------------

 - **[TODO]** (PIM-5519) Mass edit products, display the add to a group configuration is too long with a lot of product groups (use a paginated select2 and not checkboxes)
 - **[TODO]** (PIM-5520) Mass edit products, display the add to a variant group configuration is too long with a lot of product groups (use a paginated select2)
 - **[TODO]** (PIM-5467) When saving a variant group, variant group values are synchronously copied in products, it may cause timeout issue
 - **[TODO]** (PIM-5463) When associating a lot of products to a group, variant group or association, you may encounter "The requested URL's length exceeds the capacity"
