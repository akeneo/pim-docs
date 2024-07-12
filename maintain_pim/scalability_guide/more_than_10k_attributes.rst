More than 10k attributes?
-------------------------

The number of attributes may impact performances of Akeneo PIM in several ways. 
We’ve tested performances with a set of 10k simple attributes in total (not 10k attributes per product). Note: These attributes were not scopable nor localizable.

.. warning::

    We've already improved different screens and processes but we are still experiencing different issues with 10k attributes and a low amount of families and categories.

    The impacted screens are the following:
     - **[Fixed v1.6.0]** (PIM-5401) the attributes popin in variant group page (Community Edition)
     - **[Fixed v1.6.0]** (PIM-5283) use the same edit form system than product for the variant group edit (Community Edition)
     - **[Fixed v1.7.0]** (PIM-5212) the attributes popin in family edit page (Community Edition)
     - **[TODO]** (PIM-6118) the configure step of the family / mass edit / requirements (Community Edition)
     - **[TODO]** (PIM-6094) the attributes popin in attribute group edit page (Community Edition)

    **If you plan to use the PIM with more than 10k attributes, please contact us.**

You may be interested in :doc:`/maintain_pim/scalability_guide/more_than_500_attributes_usable_in_product_grid`
