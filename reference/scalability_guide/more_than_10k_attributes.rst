More than 10k attributes?
-------------------------

The number of attributes may impact performances in Akeneo PIM in several ways.
We've tested performances with a set of 10k attributes in total (not 10k attributes per product).

.. note::

    *All the problems listed below will be fixed in the upcoming 1.4.x patches.*
    If you encounter one of these problems, don't hesitate to take regularly a look at our `changelog`_. If you encounter another problem, please contact us.

.. _changelog: https://github.com/akeneo/pim-community-dev/blob/1.4/CHANGELOG-1.4.md

.. warning::

    On this axis, we still have different issues. We've tested with 10k attributes with a low amount of families and categories.

    Screens impacted are the following, we're releasing performances fixes as 1.4 patches:
     - **[fixed in 1.4.11]** (PIM-5209) the loading of the route /configuration/attribute-group/rest (Community Edition)
     - **[fixed in 1.4.12] see following chapter** (PIM-5208) the product grid loading due to manage filters design (Community Edition)
     - **[fixed in 1.4.12] see following chapter** (PIM-5208) the variant product grid loading due to manage filters design (Community Edition)
     - **[fixed in 1.4.14]** (PIM-5210) the configure step of the product / mass edit / common attributes (Community Edition)
     - **[fixed in 1.4.14]** (PIM-5211) the edition of a variant group due to large amount of attributes usable as axis (Community Edition)
     - **[fixed in 1.4.16]** (PIM-5213) the optional attributes popin in the product edit form (Community Edition)
     - **[fixed in 1.4.16]** (PIM-5213) the attributes popin in the mass edit attributes (Community Edition)
     - (PIM-5401) the attributes popin in family edit page, attribute group page, variant group page (Community Edition)
     - (PIM-5212) the configure step of the family / mass edit / requirements (Community Edition)

    **If you plan to use the PIM with more than 10k attributes, please contact us.**
