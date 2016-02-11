More than 10k attributes?
-------------------------

The number of attributes may impact performances of Akeneo PIM in several ways.
We've tested performances with a set of 10k attributes in total (not 10k attributes per product).

.. warning::

    We've already improved different screens and processes but we are still experiencing different issues with 10k attributes and a low amount of families and categories.

    Screens impacted are the following:
     - **[TODO]** (PIM-5401) the attributes popin in family edit page, attribute group page, variant group page (Community Edition)
     - **[TODO]** (PIM-5212) the configure step of the family / mass edit / requirements (Community Edition)
     - **[TODO]** (PIM-5283) use the same edit form system than product for the variant group edit

    **If you plan to use the PIM with more than 10k attributes, please contact us.**

You may be interested in :doc:`/reference/scalability_guide/more_than_500_attributes_usable_in_product_grid`
