More than 500 attributes usable in the product grids?
-----------------------------------------------------

The number of attributes usable in the product grids will impact performances of the PIM in several ways.

We've tested performances with a set of 10k attributes, including 500 attributes usable in the grids (meaning 10k attributes in total not 10k attributes per product).

Prior to 1.4.12, all attributes could be displayed in the grid columns and only *attributes usable as grid filter* could be used to filter data in the grids. We observed a significant performance problem during the loading of the product grids for catalogs with a large number of attributes. We decided to change this behavior and to define a limitation of 500 attributes usable in the grid.

From 1.4.12 and upper versions, the attribute option *attributes usable as grid filter* became *usable in grid*.
This option states whether or not the attribute can be displayed as a column or used as a filter in product grids.

We'll try to improve the datagrid system to increase this limitation in an upcoming minor version.

.. warning::

    **If you plan to use the PIM with more than 500 attributes usable in the grids, please contact us.**
