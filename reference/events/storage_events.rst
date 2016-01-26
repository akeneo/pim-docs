Storage events
==============

Storage events are dispatched in the PIM when you manipulate data using the savers and removers services.
They are basically useful if you want to add your business logic before or after an object is saved or removed.

Here is a list of all the events:

``akeneo.storage.pre_remove``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `RemoveEvent <https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Component/StorageUtils/Event/RemoveEvent.php>`_

This event is dispatched before we remove an object using a remover.

These are the built-in PIM subscribers registered to this event:

================================================================  ===============
Listener Class Name                                               Priority
================================================================  ===============
`CheckChannelsOnDeletionSubscriber`_                              0
`ProductTemplateAttributeSubscriber`_                             0
RuleRelationSubscriber (Enterprise edition)                       0
RemoveChannelSubscriber (Enterprise edition)                      0
CheckPublishedProductOnRemovalSubscriber (Enterprise edition)     0
================================================================  ===============

.. _CheckChannelsOnDeletionSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Pim/Bundle/CatalogBundle/EventSubscriber/Category/CheckChannelsOnDeletionSubscriber.php
.. _ProductTemplateAttributeSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Pim/Bundle/CatalogBundle/EventSubscriber/ProductTemplateAttributeSubscriber.php

``akeneo.storage.post_remove``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `RemoveEvent <https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Component/StorageUtils/Event/RemoveEvent.php>`_

This event is dispatched after we remove an object using a remover.

These are the built-in PIM subscribers registered to this event:

===================================================  ===============
Listener Class Name                                  Priority
===================================================  ===============
`AddRemoveVersionSubscriber`_                        0
`RemoveOutdatedProductsFromAssociationsSubscriber`_  0
`ProductCategorySubscriber`_                         0
===================================================  ===============

.. _AddRemoveVersionSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Pim/Bundle/VersioningBundle/EventSubscriber/AddRemoveVersionSubscriber.php
.. _RemoveOutdatedProductsFromAssociationsSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Pim/Bundle/CatalogBundle/EventSubscriber/MongoDBODM/RemoveOutdatedProductsFromAssociationsSubscriber.php
.. _ProductCategorySubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Pim/Bundle/CatalogBundle/EventSubscriber/ProductCategorySubscriber.php

``akeneo.storage.pre_save``
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

This event is dispatched before we save an object using a saver.

These are the built-in PIM subscribers registered to this event:

==============================  ===============
Listener Class Name             Priority
==============================  ===============
`CurrencyDisablingSubscriber`_  0
==============================  ===============

.. _CurrencyDisablingSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Pim/Bundle/CatalogBundle/EventSubscriber/CurrencyDisablingSubscriber.php

``akeneo.storage.post_save``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

This event is dispatched after we save an object using a saver.

``akeneo.storage.pre_save_all``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

This event is dispatched before we save a pool of objects using the saveAll function of the saver.

``akeneo.storage.post_save_all``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

This event is dispatched after we save a pool of objects using the saveAll function of the saver.
