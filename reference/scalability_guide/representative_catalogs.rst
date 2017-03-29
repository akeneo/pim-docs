Audit with 3 Representative Catalogs
====================================

.. warning::

    This page is an early version, we'll continue to complete it with more use cases.

We've audited the application with 3 different representative catalogs:

+-----------------------------------------+-----------+------------+-------------+
| **Catalog**                             | **Small** | **Medium** | **Large**   |
+-----------------------------------------+-----------+------------+-------------+
| Products                                | 5.000     | 50.000     | 1.000.000   |
+-----------------------------------------+-----------+------------+-------------+
| Categories                              | 500       | 2.000      | 4.000       |
+-----------------------------------------+-----------+------------+-------------+
| Categories / product                    | 2         | 2          | 4           |
+-----------------------------------------+-----------+------------+-------------+
| Attributes                              | 100       | 400        | 1.000       |
+-----------------------------------------+-----------+------------+-------------+
| Attribute Groups                        | 8         | 15         | 20          |
+-----------------------------------------+-----------+------------+-------------+
| Attributes / Families                   | 50        | 100        | 100         |
+-----------------------------------------+-----------+------------+-------------+
| % filled attributes                     | 75%       | 75%        | 50%         |
+-----------------------------------------+-----------+------------+-------------+
| %localisable attributes                 | 10%       | 5%         | 2%          |
+-----------------------------------------+-----------+------------+-------------+
| %scopable attributes                    | 5%        | 2%         | 1%          |
+-----------------------------------------+-----------+------------+-------------+
| %scopable + localisable attributes      | 2%        | 1%         | < 1%        |
+-----------------------------------------+-----------+------------+-------------+
| Families                                | 20        | 50         | 400         |
+-----------------------------------------+-----------+------------+-------------+
| Channels                                | 2         | 2          | 2           |
+-----------------------------------------+-----------+------------+-------------+
| Enabled Locales                         | 1         | 4          | 4           |
+-----------------------------------------+-----------+------------+-------------+
| **Audit Status for Community Edition**  | **Ok**    | **Ok**     | **Ok**      |
+-----------------------------------------+-----------+------------+-------------+
| **Audit Status for Enterprise Edition** | **Ok**    | **Ok**     | **Ok**      |
+-----------------------------------------+-----------+------------+-------------+

These catalogs are available in our dedicated repository https://github.com/akeneo/catalogs, you can also use our data generator to build your own test catalog https://github.com/akeneo-labs/DataGeneratorBundle.

Several of our customers strongly push these limitations in their custom projects, you can consult different use cases in this page. We adapt these representative catalogs between minor versions when we improve the application scalability. Don't hesitate to consult this page for other versions.

How we tested?
--------------

**Installation**

The application is installed on a server following the recommended architecture :doc:`/reference/technical_information/index`.

Depending on the catalog, we used a different database storage. We installed the data `fixtures` via the installer before importing the products through the default product csv import job (for a large product import, we split the catalog into 10 files + parallel imports + custom optimisations).

The targeted amount of product values implies to choose a relevant database storage, if you want to know more about how to choose the right storage strategy, please read :doc:`/reference/scalability_guide/more_than_5M_product_values`.

Depending on the data volume, the number of field indexes in MongoDB also impacts the performances, please read :doc:`/reference/scalability_guide/more_than_64_indexes_with_mongodb` for more information.

+---------+---------+----------------+--------------------------------------------------------------------------------------------------------------------------+
|         | Storage | Product values | Note                                                                                                                     |
+---------+---------+----------------+--------------------------------------------------------------------------------------------------------------------------+
| Small   | MySQL   | 159.676        |                                                                                                                          |
+---------+---------+----------------+--------------------------------------------------------------------------------------------------------------------------+
| Medium  | MySQL   | 3.661.981      |                                                                                                                          |
+---------+---------+----------------+--------------------------------------------------------------------------------------------------------------------------+
| Large   | MongoDB | 52.699.463     | **With more than ~300k products or ~15M product values, you should use the additional ElasticSearchBundle (contact us)** |
+---------+---------+----------------+--------------------------------------------------------------------------------------------------------------------------+

**Audit User Interface**

We use the application in production mode, with xdebug disabled, and we expect an optimal user experience for each page and action.

**Audit Backend Processes**

We run backend processes (bulk actions, imports, exports, rules execution, etc) in production mode, with xdebug disabled. Processes may run for quite a long time depending on the amount of data but do not consume more memory than the volume advised in :doc:`/reference/technical_information/index`. Please note that for some project data set, several extra configurations are required (for instance, changing the size of a bulk of products for the rules execution).

**Automation**

We have built several tools to automate these performance and scalability tests. Basically, our continuous integration loads a target catalog and then runs different scenarios. The build is considered to fail when thresholds on time execution and memory usage are reached. These tools are not open sourced for now.

Known limitations on representative catalogs
--------------------------------------------

We observed two kinds of limitations during the audit: memory leaks and scalability limitations.

Memory leaks when a process consumes more memory than recommended. These issues are qualified as bugs, their fixes are released in version patches.

Scalability limitations, when we try to support larger data volume for an axis. These issues are qualified as improvements which are released in minor versions.

The following limitations have been encountered with standard installations without any custom code:

+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| **Type** | **Catalog** | **Edition** | **Released** | **Note**                                                                                                                                                       |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| memleak  | All         | All         | v1.4.23      | (PIM-5507) Memory leak when executing mass edit or mass publish on several thousands of products                                                               |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | Medium      | All         | TODO         | (PIM-5467) First load of the completeness widget is too long (ORM)                                                                                             |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | Large       | All         | TODO         | (PIM-5518) Timeout with synchronous update of products when remove 'AssociationType', 'Attribute', 'AttributeOption', 'Category', 'Family', 'Group', 'Channel' |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | Large       | All         | v1.6.0       | (PIM-5542) the request /configuration/family/rest slow down the UI (dashboard, grid, pef)                                                                      |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | Large       | Enterprise  | TODO         | (PIM-5544) the request /enrich/product-category-tree/list-tree.json allowing to load the tree on the grid is very slow (improved with Elastic Search Bundle)   |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | Large       | All         | TODO         | MongoDB timeout when filtering and sorting on product grid when using not indexed fields (improved with Elastic Search Bundle)                                 |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+

Examples of customers instance
------------------------------

Several customers challenge the limitations even more in their custom projects and it requires custom optimizations sometimes. We continuously improve the product scalability in each minor version and we are always interested in new use cases to investigate. Don't hesitate to contact us if you need help to scale your instance.

**On standard axes:**

+-----------------------------------------+-----------------+-----------------+----------------+----------------+--------------------------------------------------------------+
| **Catalog**                             | **Customer 1**  | **Customer 2**  | **Customer 3** | **Customer 4** | **Details about limitations**                                |
+-----------------------------------------+-----------------+-----------------+----------------+----------------+--------------------------------------------------------------+
| Storage                                 | MongoDB + ES    | MongoDB + ES    | MySQL          | MySQL          | ES: ElasticSearch Bundle                                     |
+-----------------------------------------+-----------------+-----------------+----------------+----------------+--------------------------------------------------------------+
| Products                                | **2.000.000**   | 1.100.041       | **80.000**     | 10.000         |                                                              |
+-----------------------------------------+-----------------+-----------------+----------------+----------------+--------------------------------------------------------------+
| Products values                         | 43.398.847      | **78.606.501**  | **6.000.000**  | 70.000         | 6 millions product values is a high limit for MySQL storage  |
+-----------------------------------------+-----------------+-----------------+----------------+----------------+--------------------------------------------------------------+
| Attributes                              | 1.800           | **8.272**       | 240            | 355            | :doc:`/reference/scalability_guide/more_than_10k_attributes` |
+-----------------------------------------+-----------------+-----------------+----------------+----------------+--------------------------------------------------------------+
| Families                                | 131             | **3.546**       | 44             | 3              | :doc:`/reference/scalability_guide/more_than_10k_families`   |
+-----------------------------------------+-----------------+-----------------+----------------+----------------+--------------------------------------------------------------+
| Categories                              | 2613            | **14.238**      | 740            | 60             | :doc:`/reference/scalability_guide/more_than_10k_categories` |
+-----------------------------------------+-----------------+-----------------+----------------+----------------+--------------------------------------------------------------+
| Channels                                | 1               | 2               | 2              | **14**         |                                                              |
+-----------------------------------------+-----------------+-----------------+----------------+----------------+--------------------------------------------------------------+
| Enabled Locales                         | 1               | 1               | **36**         | 1              |                                                              |
+-----------------------------------------+-----------------+-----------------+----------------+----------------+--------------------------------------------------------------+

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
| Products per family                | [WIP]      | 1.000.000             | cf following PIM-5563                                                   |
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
| Product associations               | [WIP]      |                       | cf following PIM-5363, PIM-5562                                         |
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

**Known limitations on other axes or combinations**

+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| **Type** | **Catalog** | **Edition** | **Released** | **Note**                                                                                                                                                       |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | All         | All         | TODO         | (PIM-5519) Mass edit products, display the add to a group configuration is too long with a lot of product groups (use a paginated select2 and not checkboxes)  |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | All         | All         | TODO         | (PIM-5520) Mass edit products, display the add to a variant group configuration is too long with a lot of product groups (use a paginated select2)             |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | All         | All         | TODO         | (PIM-5467) When saving a variant group, variant group values are synchronously copied in products, it may cause timeout issue                                  |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | All         | All         | TODO         | (PIM-5463) When associating a lot of products to a group, variant group or association, you may encounter "The requested URL's length exceeds the capacity"    |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | All         | All         | TODO         | (PIM-5562) When delete a product associated to other products, run a backend process to cleanup all associations                                               |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | All         | All         | TODO         | (PIM-5563) Query for completeness rescheduling when saving a family with 50k products inside is too long to execute, exec as backend process                   |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+
| improv.  | All         | All         | TODO         | (IM-766) Variant groups scalability: limit number of axes or set a limit?                                                                                      |
+----------+-------------+-------------+--------------+----------------------------------------------------------------------------------------------------------------------------------------------------------------+ 

