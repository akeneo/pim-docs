Abstract Entities and Repositories
==================================

The bundle provides a series of abstract entities.

Entity\\AbstractCustomEntity
----------------------------

This entity implements the basic interfaces that are used in Akeneo. It defines a "code" property which is
used throughout the PIM as a reference.

The repositories for this type of entity should extend
``Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository``.


Entity\\AbstractTranslatableCustomEntity
----------------------------------------

This entity extends the AbstractCustomEntity, and provides a link to a translation entity. This is used
to provide a different label for each activated locale.

The entity containing the translations should implement ``Akeneo\Component\Localization\Model\TranslatableInterface``.

The repositories for this type of entity should extend
``Pim\Bundle\CustomEntityBundle\Entity\Repository\TranslatableCustomEntityRepository``.
