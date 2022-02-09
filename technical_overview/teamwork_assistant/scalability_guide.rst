Teamwork Assistant Scalability Guide
____________________________________

For the moment, the pre-processing data are computed once you save a project, when you save a product concerned by a
project from the Product Edit Form and the sequential edit. Moreover, a command
``php bin/console pimee:project:recalculate`` is provided to help you to recalculate data according to your needs.

This scalability guide will focus on how to use the Teamwork Assistant functionally (how much projects in the same time,
etc.) and technically (how to use the recalculate command) in a healthy way.

.. note::

    To know more about the project completeness visit the :doc:`project completeness <project_completeness>` reference.

Computing Project Completeness
------------------------------

Before beginning to work with the Teamwork Assistant, you must be aware that this feature has to compute a lot of
data. The more products and attribute groups you have in a project, the more time project completeness computing will take.
We did some benchmarks with our :doc:`medium catalog </maintain_pim/scalability_guide/representative_catalogs>`
on MySQL to give you an idea of the time it can take. A medium
catalog is about 3,66 millions of product values.


In this benchmark the catalog contained 5 users with read/edit permissions on all 15 attribute groups and 5 users
with read/edit permissions on 7 attribute groups.

+------------------------------------+-----------------------+
| Project size (products)            | time spent to compute |
+====================================+=======================+
| 50k products (3,6m product values) | 3h 36m 30s            |
| 50k products already calculated    | 45m 30s               |
| 5k products                        | 19 min 10 sec         |
| 5k products already calculated     | 4 min 18 sec          |
+------------------------------------+-----------------------+

Here are the server’s features used for the bench:
**CPU:** Intel E3-1220 V2 @ 3.1GHZ (4 cores)
**RAM:** 16 GB
**Disk:** SATA 3 - 7200 RPM disks
