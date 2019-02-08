Configure Entity Limits
=======================

For the Reference Entities feature (*introduced in 3.0*), some limits about number of entities have been defined to guarantee that the PIM is functional and runs smoothly.
However, as your catalog is very unique, **you may need to raise those values**, this chapter explains how.

.. warning::
   If you hit those limits, this could reflect that **you may need to change your modelisation**

All those limits are defined as parameters that you can override like any other `Symfony config parameter <https://symfony.com/doc/3.4/best_practices/configuration.html>`_.

Raise the limit of Reference Entities
-------------------------------------
By default, you can't create more than **100 Reference Entities**.
If you want to create more, you have to edit the ``reference_entity_limit`` parameter, for example:

.. code-block:: yaml
    :linenos:

    # app/config/parameters.yml
    parameters:
        reference_entity_limit: 200

Raise the limit of Attributes per Reference Entity
--------------------------------------------------
By default, you can't create more than **100 Attributes per Reference Entity**.
If you want to create more, you have to edit the ``reference_entity_maximum_attribute`` parameter, for example:

.. code-block:: yaml
    :linenos:

    # app/config/parameters.yml
    parameters:
        reference_entity_maximum_attribute: 130

Raise the limit of Options per List Attribute
---------------------------------------------
By default, you can't create more than **100** **Options per List Attribute**.
If you want to create more, you have to edit the ``reference_entity_option_limit_per_list_attribute`` parameter, for example:

.. code-block:: yaml
    :linenos:

    # app/config/parameters.yml
    parameters:
        reference_entity_option_limit_per_list_attribute: 200


Raise the limit of Records per Reference Entity
-----------------------------------------------
By default, you can't create more than **1 000 000** (*one million*) **Records per Reference Entity**.
If you want to create more, you have to edit the ``reference_entity_record_limit_per_reference_entity`` parameter, for example:

.. code-block:: yaml
    :linenos:

    # app/config/parameters.yml
    parameters:
        reference_entity_record_limit_per_reference_entity: 2000000
