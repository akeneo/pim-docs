Doctrine events [WIP]
=====================

By the past, we've plugged a lot of ou business code on Doctrine events (prePersist, preUpdate, onFlush, postFlush).

For instance, to create versions, to convert metric values, to update properties 'created at' and 'updated at', etc.

This practice strongly couple our business code to Doctrine entity lifecyle and causes several performance issues.

In 1.4,
 - we've introduced our own business events that are dispatched by Saver, BulkSaver, Remover, BulkRemover
 - we've continued to use these Saver, BulkSaver, Remover, BulkRemover

The strategy is to use our own business events to plug the business logic that was relying on doctrine events.

## Doctrine repositories [WIP]

In very early version of the Akeneo PIM, we've used Doctrine repository in a quite standard way.

We define them by using the Doctrine factory service:

```
    pim_catalog.repository.attribute:
        class: %pim_catalog.repository.attribute.class%
        factory_service: doctrine.orm.entity_manager
        factory_method: getRepository
        arguments: [%pim_catalog.entity.attribute.class%]
        tags:
            - { name: 'pim_repository' }
```

In the code we fetched them by using the following methods of the Doctrine ObjectManager:

```
    $entityManager->getRepository('PimCatalogBundle:Attribute')
    $entityManager->getRepository('Pim\Bundle\CatalogBundle\Entity\Attribute')
```

With our following versions, this practice shows limitation, it forbid to override models in project.

So, in 1.2, 1.3, 1.4 versions we've continuously replaced '$entityManager->getRepository(' by the injection of the service repository.

We're getting rid of factory service to instanciate new repositories as standard services to be able to have several repositories for an object.

For instance, a product repository in catalog bundle, another one in enrich with methods related to grid and forms, etc.

It allows a better separation of concerns and a more atomic customization in projects.
