Teamwork Assistant Scalability Guide
____________________________________

For the moment, the pre-processing data are computed once you save a project, when you save a product concerned by a
project from the Product Edit Form and the sequential edit. Moreover, a command
``php app/console pimee:project:recalculate`` is provided to help you to recalculate data according to your needs.

This scalability guide will focus on how to use the Teamwork Assistant functionally (how much projects in the same time,
etc.) and technically (how to use the recalculate command) in a healthy way.

.. _project completeness: project_completeness.html

.. note::

    To know more about the project completeness visit the `project completeness`_ reference.

Compute Project Completeness
----------------------------

.. _medium catalog: ../scalability_guide/representative_catalogs.html

Before beginning to work with the Teamwork Assistant, you have to be aware that this feature has to compute a lot of
data. The more products and attribute groups you have in a project, the more project completeness computing will take.
We realized some benchmarks with our `medium catalog`_ on MySQL to give you an idea of the time it can take. A medium
catalog is about 3,66 millions of product values.

+-------------------------+-----------------------+
| Project size (products) | time spent to compute |
+=========================+=======================+
|