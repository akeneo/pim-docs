Scalability Guide
=================

Your product catalog is unique, with different amount of attributes, families, locales, channels, products, etc all those different combinations may cause different scalability issues.

The following chapter explains the different scalability bottlenecks you can encounter by using *Akeneo PIM* and lists known issues. It also explains best practices you should implement to ensure the success of your project.

The amount of data the PIM handles can evolve for each new minor version, thank you to contact us when your use case is not covered, we can handle it in next minor version or provide you an alternative solution for your project.

.. warning::

    This is an early version of this chapter, we'll continue to complete it with more use cases.
    We also provide a :doc:`/reference/performances_guide/index`.

.. note::

    *Akeneo PIM* has grown and evolved quickly. We added a lot of useful and cool features since the first release.
    At this time, *Akeneo PIM* was mostly used with a catalog of maybe some hundreds of attributes and some tens of families. It has completely changed, our standard catalog target switched to a far higher number of attributes and families, whereas *Akeneo PIM* was not benched with such catalogs.
    Performance is a feature, and we know it. That's why we have performed following audits of the whole 1.4 application, axis by axis, for both versions (Community and Enterprise).
    You'll find bottlenecks that we have encountered and related improvements.

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
