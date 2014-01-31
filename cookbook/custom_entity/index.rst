How to Create a Custom Entity and Use it as an attribute
========================================================

Why Create a Custom Entity ?
----------------------------

In some cases, you may need to manage different entities than the one natively
provided by Akeneo and link them to the products.

.. note::

    if you do not need to use your custom entity as an attribute, follow
    only the first part of this cookbook's recipe.

    the following code is compatible with beta-4 version, will be deprecated with rc-1 (doc will be updated soon).


For example, let's say we want to create a more advanced manufacturer entity
than using a standard attribute option, because the manufacturer needs
specific attribute itself like the manufacturing country.

* Creating the entity and the associated screens
    * :doc:`creating_a_custom_entity`

* Creating an attribute type linked to this custom entity
    * :doc:`creating_an_attribute_type`

* Importing your custom entity
    * :doc:`importing_custom_entities`
