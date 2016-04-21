How to Customize Validation
===========================

Overriding existing validation
------------------------------

To override existing validation rules, you will have to copy the existing validation to your own bundle.

For example, to allow spaces in option codes, create the following file:

.. literalinclude:: ../../src/Acme/Bundle/CatalogBundle/Resources/config/validation/attribute.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/CatalogBundle/Resources/config/validation/attribute.yml
   :emphasize-lines: 189-202
   :linenos:

As you can see, all the validation rules must be copied back to your file.


Adding new validation rules
---------------------------

To add new validation rules instead of replacing existing ones, simply create a new yml file in
the ``config/validation`` directory of your bundle. The name of the file **must** be different
from already existing validation configuration files.
