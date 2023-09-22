Configure Entity Limits
=======================

For the Reference Entities feature (*introduced in 3.0*), some limits about the number of entities have been defined to guarantee that the PIM is functional and runs smoothly.
However, as your catalog is unique, **you may need to raise those values**, this chapter explains how.

.. warning::
    If you hit those limits, this could reflect that **your modelization is not adapted**. If it's the case, we can't guarantee that the PIM will be functional and runs smoothly, be sure to test the performances with your new limits.

All those limits are defined as parameters that you can override like any other `Symfony config parameter <https://symfony.com/doc/5.4/best_practices/configuration.html>`_.

Raise the limit of Reference Entities
-------------------------------------
By default, you can't create more than **200 Reference Entities**.
If you want to create more, you have to edit the ``reference_entity_limit`` parameter, for example:

.. code-block:: yaml
    :linenos:

    # app/config/parameters.yml
    parameters:
        reference_entity_limit: 250

Raise the limit of Attributes per Reference Entity
--------------------------------------------------
By default, you can't create more than **100 Attributes per Reference Entity**.
If you want to create more, you have to edit the ``reference_entity_maximum_attribute`` parameter, for example:

.. code-block:: yaml
    :linenos:

    # app/config/parameters.yml
    parameters:
        reference_entity_maximum_attribute: 130

Raise the limit of Options for "Simple Option" and "Multiple Option" Attributes
-------------------------------------------------------------------------------
You can't create more than **100** options for each **"Simple Option" and "Multiple Option"**.
If you need more than 100 options, we advise you to create a dedicated reference entity.


Raise the limit of Records per Reference Entity
-----------------------------------------------
By default, you can't create more than **1 000 000** (*one million*) **Records per Reference Entity**.
If you want to create more, you have to edit the ``reference_entity_record_limit_per_reference_entity`` parameter, for example:

.. code-block:: yaml
    :linenos:

    # app/config/parameters.yml
    parameters:
        reference_entity_record_limit_per_reference_entity: 2000000
