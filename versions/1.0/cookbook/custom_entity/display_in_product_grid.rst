How to display the custom entity in the product grid
====================================================

Using a vendor column and filter in product grid
------------------------------------------------

To add the vendor column in the product grid, you have to define each part (column, filter, sorter)
in a specific file definition ``grid_attribute_types.yml``:

.. literalinclude:: ../../src/Acme/Bundle/IcecatDemoBundle/Resources/config/grid_attribute_types.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/IcecatDemoBundle/Resources/config/grid_attribute_types.yml
   :linenos:


