Scalability Guide
=================

Your product catalog is unique, with different amount of products, attributes, families, locales, channels, etc all. Those different combinations may affect the application performances.

This page explains how we audit the application scalability, what are the bottlenecks already encountered and lists known issues and improvements. It also explains best practices you should implement to ensure the success of your project.

We improve the scalability in each new minor versions. If you encounter any new limitation, please does not hesitate to contact us. We'll be able to give you details about the roadmap for this specific improvement and we'll help you to find an alternative solution for your project.

.. warning::

    This is an early version of this chapter, we'll continue to complete it with more use cases.
    We also provide a :doc:`/reference/performances_guide/index`.

.. toctree::
    :maxdepth: 1

    representative_catalogs
    more_than_10k_attributes
    more_than_10k_families
    more_than_10k_categories
    more_than_5M_product_values
    more_than_64_indexes_with_mongodb
    more_than_500_attributes_usable_in_product_grid
    more_than_100k_products_to_export
