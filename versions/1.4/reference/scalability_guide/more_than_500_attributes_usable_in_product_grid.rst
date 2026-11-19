More than 500 attributes usable in the product grids?
-----------------------------------------------------

The number of attributes usable in the product grids will impact performances of the PIM in several ways.

We've tested performances with a set of 10k attributes, including 500 attributes usable in the grids (meaning 10k attributes in total not 10k attributes per product).

Prior to 1.4.12, all attributes could be displayed in the grid columns and only *attributes usable as grid filter* could be used to filter data in the grids. We observed a significant performance problem during the loading of the product grids for catalogs with a large number of attributes.

As we couldn't rework the way the grids are configured in a patch, we decided to change this behavior and we'll improve it in an upcoming minor version.

From 1.4.12 and upper versions, the attribute option *attributes usable as grid filter* became *usable in grid*.
This option states whether or not the attribute can be displayed as a column or used as a filter in product grids.

.. warning::

    Screens impacted are the following:
      - **[improved in 1.4.12]** (PIM-5208) the product grid loading due to manage filters design (Community Edition)
      - **[improved in 1.4.12]** (PIM-5208) the variant product grid loading due to manage filters design (Community Edition)

    **If you plan to use the PIM with more than 500 attributes usable in the grids, please contact us.**
