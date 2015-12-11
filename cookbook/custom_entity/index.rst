How to create Custom Entities
=============================

In some cases, you may need to manage different entities than the one natively
provided by Akeneo and link them to the products.

For example, if your products are provided by suppliers, you may want them
to be selected in the product edit form as a simple select list.

You'd use an attribute called **Supplier** containing the full list of suppliers.

Suppose now that you want to have extra informations for each suppliers, such as :
 * code
 * name of the supplier
 * contact informations (phone numer, adress, mail)
 * country
 * logo

You can't perform this with an attribute. Here come the custom entities!

In this cookbook, you will learn how to:
 * :doc:`installation`
 * :doc:`creating_a_custom_entity`
 * Link products to a custom entity
 * Link a custom entity to another custom entity 
 * Validation of custom entity
 * Set ACLs
 * Use Historization
 * Perform Imports and Exports

.. note::
   All the code from this cookbook is available in the example folder of the
   Custom Entity Bundle GitHub repository.

.. note::
    API documentation of the Custom Entity Bundle:
    https://github.com/akeneo/CustomEntityBundle/blob/master/Resources/doc/index.rst
