More than 10k attributes?
-------------------------

The number of attributes may impact performances of Akeneo PIM in several ways.
We've tested performances with a set of 10k attributes in total (not 10k attributes per product).

.. note::

    If you encounter any of these problems, don't hesitate to regularly take a look at our `changelog`_. If you encounter another problem, please contact us.

.. _changelog: https://github.com/akeneo/pim-community-dev/blob/1.4/CHANGELOG-1.4.md

.. warning::

    We are still experiencing different issues with 10k attributes and a low amount of families and categories.

    Screens impacted are the following:
     - **[improved in 1.4.11]** (PIM-5209) the loading of the route /configuration/attribute-group/rest (Community Edition)
     - **[improved in 1.4.12] see 500 attributes in grid** (PIM-5208) the product grid loading due to manage filters design (Community Edition)
     - **[improved in 1.4.12] see 500 attributes in grid** (PIM-5208) the variant product grid loading due to manage filters design (Community Edition)
     - **[improved in 1.4.14]** (PIM-5210) the configure step of the product / mass edit / common attributes (Community Edition)
     - **[improved in 1.4.14]** (PIM-5211) the edition of a variant group due to large amount of attributes usable as axis (Community Edition)
     - **[improved in 1.4.16]** (PIM-5213) the optional attributes popin in the product edit form (Community Edition)
     - **[improved in 1.4.16]** (PIM-5213) the attributes popin in the mass edit attributes (Community Edition)
     - **[TODO]** (PIM-5401) the attributes popin in family edit page, attribute group page, variant group page (Community Edition)
     - **[TODO]** (PIM-5212) the configure step of the family / mass edit / requirements (Community Edition)
     - **[TODO]** (PIM-5283) use the same edit form system than product for the variant group edit

    **If you plan to use the PIM with more than 10k attributes, please contact us.**

You may be interested in :doc:`/reference/scalability_guide/more_than_500_attributes_usable_in_product_grid`
