Scalability Guide
=================

A product catalog is unique to one's need. It is configured with a custom number of products, attributes, families, locales, channels, etc. Those different combinations may affect the application performances.

This chapter explains how we audit the application's scalability, it lists the known issues and possible improvements as well as the bottlenecks already encountered by our partners with the PIM. It also shows best practices you should implement to ensure the success of your project.

We improve the scalability in each new minor version.
If you encounter any new limitation, please do not hesitate to contact us through `the helpdesk <https://helpdesk.akeneo.com>`_.
We'll give you details about the roadmap for specific improvements and help you to find a suitable solution for your project.

.. warning::

    This is an early version of this chapter, we'll continue to complete it with more use cases.
    We also provide a :doc:`/technical_architecture/performances_guide/index`.

.. toctree::
    :maxdepth: 1

    representative_catalogs
    more_than_10k_attributes
    more_than_10k_families
    more_than_10k_categories
    more_than_500_attributes_usable_in_product_grid
    more_than_100k_products_to_export
    more_than_1GB_of_product_media_to_export
